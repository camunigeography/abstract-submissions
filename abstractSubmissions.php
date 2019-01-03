<?php

# Class to create an abstract submission system
require_once ('frontControllerApplication.php');
class abstractSubmissions extends frontControllerApplication
{
	# Assign defaults additional to the general application defaults
	public function defaults ()
	{
		# Specify available arguments as defaults or as NULL (to represent a required argument)
		$defaults = array (
			'div'											=> 'abstractsubmissions',
			'hostname'										=> NULL,
			'database'										=> 'abstracts',
			'table'											=> 'instances',
			'username'										=> 'abstracts',
			'password'										=> NULL,
			'organisationName'								=> NULL,
			'feedbackRecipient'								=> NULL,
			'authentication'								=> true,	// All pages require login
			'internalAuth'									=> true,
			'administrators'								=> 'administrators',
			'showTimezone'									=> 'GMT',
			'internalAuthPasswordRequiresLettersAndNumbers'	=> false,	// Not really necessary for this kind of application
			'dataDisableAuth'								=> true,
			'useEditing'									=> true,
		);
		
		# Return the defaults
		return $defaults;
	}
	
	
	# Define actions
	public function actions ()
	{
		# Specify additional actions
		$actions = array (
			'home' => array (
				'description' => false,
				'url' => '',
				'tab' => 'Home',
				'icon' => 'house',
				'requireInstances' => true,
			),
			'submit' => array (
				'description' => 'Create a new submission',
				'url' => 'submit.html',
				'tab' => 'Create a new submission',
				'icon' => 'add',
				'requireInstances' => true,
			),
			'submissions' => array (
				'description' => 'Submissions',
				'url' => '',
				'tab' => 'View/edit my submissions',
				'icon' => 'application_double',
				'requireInstances' => true,
			),
			'submission' => array (
				'description' => false,
				'url' => '%1/',
				'usetab' => 'submissions',
				'requireInstances' => true,
			),
			'instance' => array (
				'description' => 'Create a new submission',
				'url' => '',
				'usetab' => 'submit',
				'requireInstances' => true,
			),
			'editing' => array (
				'description' => false,
				'url' => 'data/',
				'tab' => 'Data editing',
				'icon' => 'pencil',
				'administrator' => true,
			),
			'dataprotection' => array (
				'description' => 'Data protection statement',
				'url' => 'dataprotection.html',
				'requireInstances' => true,
			),
			'download' => array (
				'description' => 'Download data',
				'url' => 'download.html',
				'administrator' => true,
				'parent' => 'admin',
				'subtab' => 'Download data',
				'requireInstances' => true,
			),
			'downloadcsv' => array (
				'description' => 'Download data',
				'url' => '%id.csv',
				'administrator' => true,
				'requireInstances' => true,
				'export' => true,
			),
		);
		
		# Return the actions
		return $actions;
	}
	
	
	# Database structure definition
	public function databaseStructure ()
	{
		return "
			CREATE TABLE `administrators` (
			  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Username' PRIMARY KEY,
			  `active` enum('','Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Yes' COMMENT 'Currently active?',
			  `privilege` enum('Administrator','Restricted administrator') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Administrator' COMMENT 'Administrator level'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='System administrators';
			
			CREATE TABLE `authors` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Automatic key' PRIMARY KEY,
			  `submission__JOIN__{$this->settings['database']}__submissions__reserved` int(11) NOT NULL COMMENT 'Submission ID',
			  `gender` enum('','Female','Male','Other') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Gender',
			  `title` enum('','Dr','Mr','Ms','Miss','Mrs','Mx','Prof','Prof Dr','Prof Sir','Associate Professor') COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Title',
			  `forename` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Forename',
			  `surname` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Surname',
			  `affiliation` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Affiliation',
			  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Address',
			  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'City',
			  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'State/Province',
			  `postcode` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Postal/ZIP code',
			  `telephone` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Telephone',
			  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Fax',
			  `countryOrigin` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Country of origin',
			  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'E-mail address'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Authors';
			
			CREATE TABLE `countries` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Automatic key' PRIMARY KEY,
			  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Country name',
			  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Label',
			  KEY `country` (`value`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Country names';
			
			CREATE TABLE `instances` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Automatic key' PRIMARY KEY,
			  `moniker` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Moniker (a-z, 0-9 and - only), e.g. some-conference',
			  `isVisible` enum('','Yes','No') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Whether this instance is visible on the listing page',
			  `organisationEmail` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Organisation e-mail address for correspondence',
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Title',
			  `openingDatetime` datetime NOT NULL COMMENT 'Opening date and time for submissions',
			  `closingDatetime` datetime NOT NULL COMMENT 'Closing date and time for submissions',
			  `message` text COLLATE utf8_unicode_ci COMMENT 'Special message that will appear on the create submission form page',
			  `topics` text COLLATE utf8_unicode_ci COMMENT 'Topics (if relevant) (one per line)',
			  `includeSubmittingPaperQuestion` enum('','Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Whether to include a question on submitting a paper for publication',
			  `includeDataSection` enum('','Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Whether to include a data details section',
			  `abstractCharacters` int(5) NOT NULL COMMENT 'Max number of characters for the abstract text',
			  `sessions` text COLLATE utf8_unicode_ci COMMENT 'Sessions (one per line)',
			  `keywords` text COLLATE utf8_unicode_ci COMMENT 'Keywords (one per line)',
			  `listSignup` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'E-mail address for list signup (or blank if none)',
			  `dataProtectionHtml` text COLLATE utf8_unicode_ci COMMENT 'Data protection statement',
			  UNIQUE KEY `moniker` (`moniker`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Settings for each instance';
			
			CREATE TABLE `submissions` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Submission number' PRIMARY KEY,
			  `instance__JOIN__{$this->settings['database']}__instances__reserved` int(11) NOT NULL COMMENT 'Instance no.',
			  `user__JOIN__{$this->settings['database']}__users__reserved` int(11) NOT NULL COMMENT 'User no.',
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Title of presentation',
			  `presentation` enum('Poster','Oral','Either') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Presentation preferences',
			  `session1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Session',
			  `abstract` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'The abstract',
			  `submittingPaper` enum('','Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Will you be submitting a paper for publication in the Annals?',
			  `topic1` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Topic (preference 1)',
			  `topic2` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Topic (preference 2)',
			  `keyword1` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Keyword (preference 1)',
			  `keyword2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Keyword (preference 2)',
			  `keyword3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Keyword (preference 3)',
			  `presentingAuthor` int(11) DEFAULT NULL COMMENT 'Presenting author',
			  `correspondingAuthor` int(11) DEFAULT NULL COMMENT 'Corresponding author',
			  `isComplete` tinyint(1) DEFAULT NULL COMMENT 'Whether the submission is complete',
			  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date/time when submission started'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Abstract submissions';
			
			CREATE TABLE `users` (
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Automatic key' PRIMARY KEY,
			  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Your e-mail address',
			  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Password',
			  `validationToken` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Token for validation or password reset',
			  `lastLoggedInAt` datetime DEFAULT NULL COMMENT 'Last logged in time',
			  `validatedAt` datetime DEFAULT NULL COMMENT 'Time when validated',
			  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp',
			  UNIQUE KEY `email` (`email`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Users';
		";
	}
	
	
	# Additional initialisation
	public function main ()
	{
		# Get the instances from the database
		$instancesRequired = (isSet ($this->actions[$this->action]['requireInstances']) && $this->actions[$this->action]['requireInstances']);
		if ($instancesRequired) {
			if (!$this->instances = $this->getInstances ()) {
				echo "\n<p>No instances have been <a href=\"{$this->baseUrl}/data/instances/add.html\">set up</a> yet by the administrator. Please check back later.</p>";
				#!# Inform admin
				return false;
			}
		}
		
	}
	
	
	# Home page
	public function home ()
	{
		# Start the HTML
		$html  = "\n<p><strong>Welcome.</strong> This section enables you to submit abstracts.</p>";
		$html .= "\n<p>Please create a new submission below or edit your current ones:</p>";
		
		# Get the submissions of the user
		$submissionsOfUser = $this->getSubmissionsOfUser ($this->user);
		
		# Show each instance as a heading
		foreach ($this->instances as $moniker => $instance) {
			
			# Skip if not visible
			if ($instance['isVisible'] != 'Yes') {continue;}
			
			# Show the heading
			$status = ($instance['isOpen'] ? "open until {$instance['closingDatetimeFormatted']}" . ($this->settings['showTimezone'] ? htmlspecialchars (" {$this->settings['showTimezone']}") : false) : ($instance['hasClosed'] ? 'now closed' : 'not yet open'));
			$html .= "\n<h3>" . htmlspecialchars ($instance['title']) . " ({$status})</h3>";
			
			# Show current submissions as a list
			$instanceId = $instance['id'];
			$list = array ();
			if (isSet ($submissionsOfUser[$instanceId])) {
				foreach ($submissionsOfUser[$instanceId] as $submissionId => $submission) {
					if ($submission['isComplete']) {
						$icon = $this->tick;
					} else {
						$icon = "<img src=\"/images/icons/exclamation.png\" alt=\"Not yet submitted\" class=\"icon\" />";
					}
					$list[] = "<a href=\"{$this->baseUrl}/{$moniker}/{$submissionId}/\">{$icon} #{$submissionId}: <strong>" . htmlspecialchars ($submission['title']) . '</strong>' . ($submission['isComplete'] ? ' (submitted)' : '') . '</a>';
				}
			}
			if ($instance['isOpen']) {
				$list[] = "<a href=\"{$this->baseUrl}/{$instance['moniker']}/\"><img src=\"/images/icons/add.png\" alt=\"+\" class=\"icon\" /> Create a new submission</a>";
			}
			$html .= application::htmlUl ($list, false, 'boxylist');
		}
		
		# Show the HTML
		echo $html;
	}
	
	
	# Standalone submit page
	public function submit ()
	{
		# Show the current instances
		$list = array ();
		foreach ($this->instances as $key => $instance) {
			if (($instance['isVisible'] == 'Yes') && $instance['isOpen']) {
				$list[] = "<a href=\"{$this->baseUrl}/{$key}/\"><strong>" . htmlspecialchars ($instance['title']) . "</strong>&nbsp; (open until {$instance['closingDatetimeFormatted']}" . ($this->settings['showTimezone'] ? htmlspecialchars (" {$this->settings['showTimezone']}") : false) . ')</a>';
			}
		}
		
		# If there are none, end
		if (!$list) {
			$html  = "<p>There is nothing open for submission at present.</p>";
			echo $html;
			return true;
		}
		
		# Compile the HTML
		$html  = "\n<p>Please select which you wish to submit for:</p>";
		$html .= application::htmlUl ($list, 0, 'spaced');
		
		# Show the HTML
		echo $html;
	}
	
	
	# Main page for a submission
	public function instance ($moniker)
	{
		# Start the HTML
		$html  = '';
		
		# Select the current instance
		if (!$this->instance = $this->selectInstance ($moniker)) {
			// echo "\n<p>The submission page you requested does not exist. Please check the URL above and try again.</p>";
			$this->page404 ();
			return false;
		}
		
		# Ensure it is open
		if (!$this->instance['isOpen']) {
			echo "\n<p>This submission page has now closed.</p>";
			return false;
		}
		
		# Add message if required
		if ($this->instance['message']) {
			echo "\n" . '<div class="box">';
			echo "\n\t<p>" . application::makeClickableLinks (nl2br (htmlspecialchars ($this->instance['message']))) . '</p>';
			echo "\n" . '</div>';
		}
		
		# Run the main part of the form
		if (!$result = $this->formMainSection ($html)) {
			echo $html;
			return false;
		}
		
		# Add the data
		if (!$this->databaseConnection->insert ($this->settings['database'], 'submissions', $result, false, $emptyToNull = false)) {
			$html = "<p class=\"warning\">{$this->cross} An error occured when adding the submission.</p>";
			if ($this->userIsAdministrator) {
				application::dumpData ($this->databaseConnection->error ());
			}
			echo $html;
			return false;
		}
		
		# Get the ID and create a formatted key from it
		$submissionId = $this->databaseConnection->getLatestId ();
		
		# Confirm
		$location = "{$this->baseUrl}/{$this->instance['moniker']}/{$submissionId}/";
		$html .= "\n<p>{$this->tick} The main part of your submission has been saved, as <a href=\"{$location}\"><strong>submission #{$submissionId}</strong></a>.</p>";
		application::sendHeader (301, "{$_SERVER['_SITE_URL']}{$location}");
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to get the instances from the database
	private function getInstances ()
	{
		# Assemble a query for the instances
		$query = "SELECT
			*,
			CONCAT ( LOWER(DATE_FORMAT(closingDatetime, '%l:%i%p')), DATE_FORMAT(closingDatetime, ', %D %M, %Y') ) AS closingDatetimeFormatted,
			IF( (NOW() < closingDatetime) AND (NOW() > openingDatetime), 1, '' ) AS isOpen,
			IF( (NOW() >= closingDatetime), 1, '' ) AS hasClosed /* i.e. was open previously */
		FROM {$this->settings['database']}.{$this->settings['table']}
		ORDER BY closingDatetime DESC;";
		
		# Get the instances or end
		if (!$data = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$this->settings['table']}")) {return false;}
		
		# Reindex by URL
		$instances = array ();
		foreach ($data as $id => $instance) {
			$moniker = $instance['moniker'];
			$instances[$moniker] = $instance;
		}
		
		# Return the instances
		return $instances;
	}
	
	
	# Function to get all submissions by a user
	private function getSubmissionsOfUser ($userId)
	{
		# Assemble a query for the instances
		$query = "SELECT
			*
			FROM {$this->settings['database']}.submissions
			WHERE user__JOIN__{$this->settings['database']}__users__reserved = {$userId}
			ORDER BY id DESC;";
		
		# Get the instances or end
		if (!$submissions = $this->databaseConnection->getData ($query, "{$this->settings['database']}.submissions")) {return false;}
		
		# Regroup by instance
		$submissions = application::regroup ($submissions, "instance__JOIN__{$this->settings['database']}__instances__reserved");
		
		# Return the list
		return $submissions;
	}
	
	
	# Function to select the current instance
	private function selectInstance ($id)
	{
		# Select the current instance
		if (!isSet ($this->instances[$id])) {return false;}
		
		# Create a shortcut for the instance
		$instance = $this->instances[$id];
		
		# Reformat some of the values
		$instance['topics']		= (strlen (trim ($instance['topics']))   ? preg_split ("/[\r\n]+/", trim ($instance['topics']))   : false);
		$instance['sessions']	= (strlen (trim ($instance['sessions'])) ? preg_split ("/[\r\n]+/", trim ($instance['sessions'])) : false);
		$instance['keywords']	= (strlen (trim ($instance['keywords'])) ? preg_split ("/[\r\n]+/", trim ($instance['keywords'])) : false);
		
		# Return the instance
		return $instance;
	}
	
	
	# Function to specify the fields to exclude in the main submission
	private function excludeFieldsMainSubmission (&$optional = array () /* returned by reference */)
	{
		# Core exclusions
		$core = array (
			"instance__JOIN__{$this->settings['database']}__instances__reserved",
			"user__JOIN__{$this->settings['database']}__users__reserved",
			'timestamp',
			'presentingAuthor',
			'correspondingAuthor',
			'isComplete',
		);
		
		# Exclusions based on whether the setting has any text in the field
		if (!$this->instance['topics']) {
			$optional[] = 'topic1';
			$optional[] = 'topic2';
		}
		if (!$this->instance['sessions']) {
			$optional[] = 'session1';
			$optional[] = 'session2';
		}
		if (!$this->instance['keywords']) {
			$optional[] = 'keyword1';
			$optional[] = 'keyword2';
			$optional[] = 'keyword3';
		}
		
		# Settings-based changes
		if ($this->instance['includeSubmittingPaperQuestion'] != 'Yes') {
			$optional[] = 'submittingPaper';
		}
		if ($this->instance['includeDataSection'] != 'Yes') {
			$optional[] = 'dataStorage';
			$optional[] = 'dataStorageDetails';
			$optional[] = 'metadata';
			$optional[] = 'metadataDetails';
			$optional[] = 'dataAvailability';
			$optional[] = 'dataAvailabilityDetails';
		}
		
		# Merge
		$exclude = array_merge ($core, $optional);
		
		# Return the list
		return $exclude;
	}
	
	
	
	# Function to create a form for the main details of a submission
	private function formMainSection (&$html, $data = array ())
	{
		# Instantiate the form
		$form = new form (array (
			'name' => 'abstracts',
			'displayDescriptions' => false,
			'displayRestrictions' => true,
			'formCompleteText' => false,
			'unsavedDataProtection' => true,
			'databaseConnection' => $this->databaseConnection,
			'nullText' => '',
			'submitButtonText' => 'Save and continue',
		));
		
		# Determine fields to exclude in dataBinding, based on the settings
		$exclude = $this->excludeFieldsMainSubmission ($optionalFields /* returned by reference */);
		
		# Databind the main part of the form
		$form->dataBinding (array (
			'database' => $this->settings['database'],
			'table' => 'submissions',
			'data' => $data,
			'intelligence' => true,
			'exclude' => $exclude,
			'attributes' => array (
				'title' => array ('heading' => array (3 => 'Presentation', 'p' => 'Here you need to enter the main details for your submission.</p><p>This <a href="https://en.wikipedia.org/wiki/Unicode_subscripts_and_superscripts#Superscripts_and_subscripts_block" target="_blank" title="[Link opens in a new window]">subscript/superscript characters (left table)</a> can be used to paste in real subscript/superscript characters.')),
				'presentation' => array ('type' => 'radiobuttons', ),
				'abstract' => array ('cols' => 80, 'rows' => 15, 'maxlength' => $this->instance['abstractCharacters'], ),
				'topic1' => array ('type' => 'select', 'values' => $this->instance['topics'], ),
				'topic2' => array ('type' => 'select', 'values' => $this->instance['topics'], ),
				'session1' => array ('type' => 'select', 'values' => $this->instance['sessions'], 'required' => true /* This is enabled at code level rather than database level as the presence of the field is settings-dependent */ ),
				'session2' => array ('type' => 'select', 'values' => $this->instance['sessions'], 'required' => true /* This is enabled at code level rather than database level as the presence of the field is settings-dependent */ ),
				'keyword1' => array ('type' => 'select', 'values' => $this->instance['keywords'], ),
				'keyword2' => array ('type' => 'select', 'values' => $this->instance['keywords'], ),
				'keyword3' => array ('type' => 'select', 'values' => $this->instance['keywords'], ),
				'metadata' => array ('title' => "Is there a <abbr title=\"Metadata, defined as data about data, distinguishes how, when, and by whom a particular set of data was collected, and how the data are formatted (as defined by NASA's Global Change Master Directory). The metadata standard used to create the records within the directory is based on the Directory Interchange Format (DIF) (Olsen, 2002)\">DIF (or other metadata format)</abbr> description of your data?", ),
				'dataStorage' => array ('heading' => array (3 => 'Data', 'p' => '<span class="comment"><em>(Your information on data submission will have no influence on selection of abstract.)</em></span>'), ),
			),
		));
		
		# Ensure uniqueness of sessions and keywords
		$fields = $this->databaseConnection->getFieldnames ($this->settings['database'], 'submissions');
		if ($this->instance['sessions']) {
			if (in_array ('session1', $fields) && in_array ('session2', $fields)) {
				$form->validation ('different', array ('session1', 'session2', ));
			}
		}
		if ($this->instance['keywords']) {
			$form->validation ('different', array ('keyword1', 'keyword2', 'keyword3', ));
		}
		
		# Add constraints for the data section; note that these use isSet so that if the data section is not in use, this will not cause any problems
		if ($unfinalisedData = $form->getUnfinalisedData ()) {
			
			# Details for the 'metadata' pair
			if (isSet ($unfinalisedData['metadata']) && isSet ($unfinalisedData['metadataDetails'])) {	// isSet used so that a table not using metadata will ignore this block
				if (substr_count ($unfinalisedData['metadata'], 'specify') && !strlen ($unfinalisedData['metadataDetails'])) {
					$form->registerProblem ('metadatadetails', 'Please give details for the metadata.', 'metadataDetails');
				}
			}
			
			# Details for the 'dataAvailability' pair
			if (isSet ($unfinalisedData['dataAvailability']) && isSet ($unfinalisedData['dataAvailabilityDetails'])) {	// isSet used so that a table not using dataAvailability will ignore this block
				if (($unfinalisedData['dataAvailability'] != 'Yes') && !strlen ($unfinalisedData['dataAvailabilityDetails'])) {
					$form->registerProblem ('metadatadetails', 'Please give a date for when the data will be made available.', 'dataAvailabilityDetails');
				}
			}
			
			# Ensure the date for making metadata available is in the future
			if (isSet ($unfinalisedData['dataAvailabilityDetails'])) {
				if ($unfinalisedData['dataAvailabilityDetails']) {
					$dataAvailabilityDetails = strtotime ($unfinalisedData['dataAvailabilityDetails']);
					$now = strtotime (date ('Y-m-d'));
					if ($dataAvailabilityDetails < $now) {
						$form->registerProblem ('dataAvailabilityDetails', 'The date for making the data accessible cannot be in the past.');
					}
				}
			}
		}
		
		# Process the form
		if (!$result = $form->process ($html)) {return false;}
		
		# Add fixed data
		$result["instance__JOIN__{$this->settings['database']}__instances__reserved"] = $this->instance['id'];
		$result["user__JOIN__{$this->settings['database']}__users__reserved"] = $this->user;
		
		# Initialise empty fields, where they exist in the field structure
		foreach ($optionalFields as $field) {
			if (in_array ($field, $fields)) {
				if (!array_key_exists ($field, $result)) {
					$result[$field] = '';
				}
			}
		}
		
		# Return the result
		return $result;
	}
	
	
	# Function to create a form to determine the authors (i.e. first stage)
	private function formAuthorSection (&$html, $data = array ())
	{
		# Clone the author if required
		if ($clone = $this->authorCloneSelectForm ($html, $data)) {
			$data = $clone;
		}
		
		# Instantiate the form
		$formAttributes = array (
			'name' => 'abstracts',
			'displayDescriptions' => false,
			'displayRestrictions' => false,
			'formCompleteText' => false,
			'unsavedDataProtection' => true,
			'databaseConnection' => $this->databaseConnection,
			'submitButtonText' => 'Save and continue',
		);
		$form = new form ($formAttributes);
		
		# Databind the authors part of the form
		$form->dataBinding (array (
			'database' => $this->settings['database'],
			'table' => 'authors',
			'data' => $data,
			'intelligence' => true,
			'exclude' => array ('id', "submission__JOIN__{$this->settings['database']}__submissions__reserved", ),
			'attributes' => array (
				'countryOrigin' => array ('autocomplete'	=> $this->dataUrl, 'autocompleteOptions' => array ('delay' => false /* Note that dataDisableAuth must be enabled, otherwise sessions get lost; even delay=150 causes the problem */ , ), ),
				'countryResidence' => array ('autocomplete'	=> $this->dataUrl, 'autocompleteOptions' => array ('delay' => false /* Note that dataDisableAuth must be enabled, otherwise sessions get lost; even delay=150 causes the problem */ , ), ),
			),
		));
		
		# Process the form
		if (!$result = $form->process ($html)) {return false;}
		
		# Return the result
		return $result;
	}
	
	
	# Function to view a submission
	public function submission ($item)
	{
		# Start the HTML with a heading
		$html  = "\n<h2>View a submission</h2>";
		
		# Ensure the submission exists
		if (!$submission = $this->databaseConnection->selectOne ($this->settings['database'], 'submissions', array ('id' => $item))) {
			application::sendHeader (404);
			$html .= "\n<p>There is no such submission. Please check the URL and try again.</p>";
			echo $html;
			return false;
		}
		
		# Ensure the user has rights to view it (though admins may view)
		if (($this->user != $submission["user__JOIN__{$this->settings['database']}__users__reserved"]) && !$this->userIsAdministrator) {
			#!# Should be here: application::sendHeader (403);
			$html .= "\n<p>The submission number you requested (#{$submission['id']}) does not appear to be your submission. Please check the URL and try again.</p>";
			echo $html;
			return false;
		}
		
		# Get the moniker
		$moniker = (isSet ($_GET['moniker']) ? $_GET['moniker'] : false);
		if (!$moniker || !isSet ($this->instances[$moniker]) || ($submission["instance__JOIN__{$this->settings['database']}__instances__reserved"] != $this->instances[$moniker]['id'])) {
			application::sendHeader (404);
			$html .= "\n<p>The page you requested does not appear to be valid. Please check the URL and try again.</p>";
			echo $html;
			return false;
		}
		
		# Determine the instance
		$this->instance = $this->selectInstance ($moniker);
		
		# Determine action
		$availableActions = array ('view', 'edit', 'delete', 'addauthor', 'viewauthor', 'editauthor', 'deleteauthor', 'confirm', );
		if ($submission['isComplete']) {$availableActions = array ('view');}	// If complete, set to be viewing only
		$action = (isSet ($_GET['do']) ? $_GET['do'] : false);
		if (!$action || !in_array ($action, $availableActions)) {
			application::sendHeader (404);
			$html .= "\n<p>The page you requested does not appear to be valid. Please check the URL and try again.</p>";
			echo $html;
			return false;
		}
		
		# Get the authors
		$this->authors = $this->getAuthors ($item);
		
		# If complete, show the submission and end
		if ($submission['isComplete']) {
			$html  = $this->viewCompletedSubmission ($moniker, $submission);
			echo $html;
			return;
		}
		
		# If the instance is no longer open, prevent editing or viewing this incomplete submission
		if (!$this->instance['isOpen']) {
			application::sendHeader (404);
			$html .= "\n<p class=\"warning\">The opening date has now passed, so your incomplete application cannot be submitted.</p>";
			echo $html;
			return false;
		}
		
		# Assign the submission title
		$this->submissionTitle = array ($submission['title']);
		
		# Generate the instance's tab entries
		$this->instanceTabs = $this->instanceTabs ($availableActions, $moniker, $item, $action, $submission);
		
		# Run the action
		$function = __FUNCTION__ . ucfirst ($action);
		$actionHtml = $this->{$function} ($submission, $moniker, $item);
		
		# Assemble the HTML, removing what was previously there
		$html  = "\n<h2>" . htmlspecialchars (implode (': ', $this->submissionTitle)) . '</h2>';
		if ($this->instanceTabs) {	// Basically mis-used as a flag for whether it is now complete
			$html .= "\n<p><img src=\"/images/icons/exclamation.png\" alt=\"Not yet submitted\" class=\"icon\" /> Not yet submitted: you must <a href=\"{$this->baseUrl}/{$moniker}/{$item}/confirm.html\">confirm and submit</a> when you have added all the authors.</p>";
		}
		$html .= $this->instanceTabsHtml ($this->instanceTabs);
		$html .= $actionHtml;
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to create tabs for an instance
	private function instanceTabs ($availableActions, $moniker, $item, $action)
	{
		# Start a list of tabs
		$tabs = array ();
		
		# Add the main page
		$location = "{$this->baseUrl}/{$moniker}/{$item}/";
		$tabs[$location] = "<a href=\"{$location}\"><img src=\"/images/icons/magnifier.png\" alt=\"\" class=\"icon\" /> Main details</a>";
		
		# Add each author
		$i = 0;
		foreach ($this->authors as $authorId => $author) {
			$i++;
			$location = "{$this->baseUrl}/{$moniker}/{$item}/{$authorId}/";
			#!# Ideally deleting an author should actually be a full regeneration, as deleting the 4th author doesn't start to display 'Author...' immediately
			$label = (count ($this->authors) < 4 ? "Author #{$i}" : "#{$i}");	// Shorten the label when too many
			$tabs[$location] = "<a href=\"{$location}\" title=\"Author {$i}\"><img src=\"/images/icons/user.png\" alt=\"\" class=\"icon\" /> {$label}</a>";
		}
		
		# Add an author addition link
		$location = "{$this->baseUrl}/{$moniker}/{$item}/addauthor.html";
		$redBorder = (!$this->authors && ($action != 'addauthor') ? ' class="redborder"' : '');
		$tabs[$location] = "<a" . $redBorder . " href=\"{$location}\" title=\"Add an author\"><img src=\"/images/icons/user_add.png\" alt=\"\" class=\"icon\" /> Add an author +</a>";
		
		# Add the confirmation page
		$location = "{$this->baseUrl}/{$moniker}/{$item}/confirm.html";
		$tabs[$location] = "<a class=\"confirm\" href=\"{$location}\"><img src=\"/images/icons/control_play_blue.png\" alt=\"\" class=\"icon\" /> Confirm and submit&hellip;</a>";
		
		# Return the tabs
		return $tabs;
	}
	
	
	# Function to generate the tabs HTML
	private function instanceTabsHtml ($tabs)
	{
		# End if no tabs
		if (!$tabs) {return '';}
		
		# Determine current location; edit.html is always a sub-page of a tab so is chopped off
		$currentLocation = preg_replace ('~^(.+)/(edit|delete).html$~', '$1/', $_SERVER['REQUEST_URI']);
		
		# Compile the HTML
		$html = "\n" . application::htmlUl ($tabs, 0, 'tabs submission', true, false, false, false, $currentLocation);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to view a submission
	private function submissionView ($submission, $moniker, $item)
	{
		# Start the HTML
		$html  = '';
		
		# Firstly show an edit link
		$html .= "<p class=\"tools\"><a href=\"{$this->baseUrl}/{$moniker}/{$item}/edit.html\">" . "<img src=\"/images/icons/pencil.png\" alt=\"\" class=\"icon\" /> Edit this page</a> | <a href=\"{$this->baseUrl}/{$moniker}/{$item}/addauthor.html\">" . "<img src=\"/images/icons/user_add.png\" alt=\"\" class=\"icon\" /> Add an author</a> | <a href=\"{$this->baseUrl}/{$moniker}/{$item}/delete.html\">" . "<img src=\"/images/icons/cross.png\" alt=\"\" class=\"icon\" /> Delete&hellip;</a></p>";
		
		# Add the main table
		$html .= $this->mainSubmissionTable ($submission);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create the main table for the submission
	private function mainSubmissionTable ($submission, $cssClass = 'lines')
	{
		# Get the table headings
		$headings = $this->databaseConnection->getHeadings ($this->settings['database'], 'submissions');
		
		# Remove internal fields
		$exclude = $this->excludeFieldsMainSubmission ();
		foreach ($exclude as $field) {
			unset ($submission[$field]);
		}
		
		# Compile the HTML
		$html  = application::htmlTableKeyed ($submission, $headings, $omitEmpty = false, $cssClass);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to edit a submission
	private function submissionDelete ($submission, $moniker, $item)
	{
		# Start the HTML
		$html  = '';
		
		# Load and create a form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'formCompleteText' => false,
			'nullText' => '',
			'display' => 'paragraphs',
			'submitButtonText' => 'Delete submission permanently',
		));
		
		# Determine the number of authors
		$authorsText = ($this->authors ? ' and ' . (count ($this->authors) == 1 ? 'its author' : (count ($this->authors) == 2 ? 'both' : 'all') . ' of its authors') : '');
		
		# Form text/widgets
		$form->heading ('p', "Do you really want to delete the submission (#{$submission['id']}) below{$authorsText}? This cannot be undone.");
		$form->select (array (
			'name'				=> 'confirmation',
			'title'				=> 'Confirm deletion',
			'required'			=> 1,
			'forceAssociative'	=> true,
			'values'			=> array ('Yes, delete this submission permanently'),
		));
		
		# Process the form
		if (!$result = $form->process ($html)) {
			$html .= $this->mainSubmissionTable ($submission, 'lines compressed');
			return $html;
		}
		
		# Delete the authors and the submission
		$this->databaseConnection->delete ($this->settings['database'], 'authors', array ("submission__JOIN__{$this->settings['database']}__submissions__reserved" => $submission['id']));
		$this->databaseConnection->delete ($this->settings['database'], 'submissions', array ('id' => $submission['id']));
		
		# Confirm deletion
		$html .= "\n<p>Submission #{$submission['id']}{$authorsText} has been deleted.</p>";
		
		# Wipe out all tabs for the instance
		$this->instanceTabs = array ();
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to edit a submission
	private function submissionEdit ($submission, $moniker, $item)
	{
		# Start the HTML
		$html  = '';
		
		# Run the main part of the form
		$formHtml = '';
		if (!$result = $this->formMainSection ($formHtml, $submission)) {
			$html .= "<p class=\"tools\"><a href=\"{$this->baseUrl}/{$moniker}/{$item}/\">" . "<img src=\"/images/icons/arrow_undo.png\" alt=\"\" class=\"icon\" /> Cancel editing</a></p>";
			$html .= $formHtml;
			return $html;
		}
		
		# Update the data
		$this->databaseConnection->update ($this->settings['database'], 'submissions', $result, array ('id' => $submission['id']));
		
		# Reassign the submission title
		$this->submissionTitle[] = $result['title'];
		
		# Confirm and issue a header
		$location = "{$this->baseUrl}/{$moniker}/{$item}/";
		$html .= "\n<p>{$this->tick} Now updated. <a href=\"{$location}\">View the updated entry.</a></p>";
		application::sendHeader (301, "{$_SERVER['_SITE_URL']}{$location}");
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to show a completed submission
	private function viewCompletedSubmission ($moniker, $submission)
	{
		# Compile the HTML
		$html  = "<h2>Completed submission (#{$submission['id']})</h2>";
		$html .= $this->completedSubmissionHtml ($moniker, $submission);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to construct a completed submission
	private function completedSubmissionHtml ($moniker, $submission)
	{
		# Determine if the submission is editable
		$isEditable = (!$submission['isComplete']);
		
		# Construct the HTML
		$html  = "<h4>Main details:" . ($isEditable ? " <span>[<a href=\"{$this->baseUrl}/{$moniker}/{$submission['id']}/edit.html\">edit further</a>]</span>" : '') . '</h4>';
		$html .= $this->mainSubmissionTable ($submission, 'lines compressed');
		$i = 0;
		foreach ($this->authors as $authorId => $author) {
			$roles = array ();
			if (!$isEditable) {
				if ($submission['presentingAuthor'] == $authorId) {$roles[] = 'Presenting author';}
				if ($submission['correspondingAuthor'] == $authorId) {$roles[] = 'Corresponding author';}
			}
			$i++;
			$authorName = htmlspecialchars ($this->authorName ($author));
			$html .= "<h4>Author #{$i} ({$authorName})" . ($roles ? ' (' . implode (' &amp; ', $roles) . ')' : '') . ':' . ($isEditable ? " <span>[<a href=\"{$this->baseUrl}/{$moniker}/{$submission['id']}/{$authorId}/edit.html\">edit further</a>]</span>" : '') . '</h4>';
			$html .= $this->authorTable ($author, 'lines compressed regulated');
		}
		
		# Surround with a div
		$html  = "\n<div class=\"graybox\">" . $html . "\n</div>";
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to finalise a submission
	private function submissionConfirm ($submission, $moniker, $item)
	{
		# Start the HTML
		$html  = '';
		
		# Update the title
		$this->submissionTitle[] = 'Confirmation';
		
		# End if no authors
		if (!$this->authors) {
			$html = "\n<p class=\"warning\">There are no authors, so you must first <a" . ($this->authors ? '' : ' class="redborder"') . " href=\"{$this->baseUrl}/{$moniker}/{$item}/addauthor.html\">" . "<img src=\"/images/icons/user_add.png\" alt=\"\" class=\"icon\" /> Add an author</a>.</p>";
			return $html;
		}
		
		# Construct the authors as a list of names
		$authorList = array ();
		$i = 0;
		foreach ($this->authors as $authorId => $author) {
			$i++;
			$authorList[$authorId] = "#{$i}: " . $this->authorName ($author);
		}
		
		# Explanation
		$html .= "\n<h3>Confirm and submit</h3>";
		$html .= "\n<p class=\"warning\"><strong>The data has not yet been submitted. You must complete the form below once all the information is ready.</strong></p>";
		
		# Add a preview of the application
		$html .= $this->completedSubmissionHtml ($moniker, $submission);
		
		# Create the form
		$html .= "\n\n<span id=\"form\"></span>";
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'formCompleteText' => false,
			'nullText' => '',
			'display' => 'paragraphs',
			'displayRestrictions' => false,
			'name' => 'formnofocus',
			'submitTo' => $_SERVER['_PAGE_URL'] . '#form',
		));
		
		# Authors
		$includeOnly = array ('presentingAuthor', 'correspondingAuthor', );
		$form->dataBinding (array (
			'database' => $this->settings['database'],
			'table' => 'submissions',
			'includeOnly' => $includeOnly,
			'attributes' => array (
				'presentingAuthor' => array ('type' => 'select', 'values' => $authorList, 'required' => true, 'forceAssociative' => true, ),
				'correspondingAuthor' => array ('type' => 'select', 'values' => $authorList, 'required' => true, 'forceAssociative' => true, ),
			),
		));
		
		# Add to list (if enabled in the settings)
		if ($this->instance['listSignup']) {
			$form->select (array (
				'name'			=> 'listSignup',
				'title'			=> "Subscribe me (" . htmlspecialchars ($this->userVisibleIdentifier) . ") to the " . htmlspecialchars ($this->settings['organisationName']) . " news mailing list?",
				'values'		=> array ('Yes', 'No', ),
				'required'		=> true,
				'description'	=> 'You will receive a confirmation e-mail, and you can unsubscribe at any time.',
			));
		}
		
		# Data protection
		if (trim ($this->instance['dataProtectionHtml'])) {
			$form->heading ('p', "In submitting this you consent to processing of the data in accordance with the <a href=\"{$this->baseUrl}/{$moniker}/dataprotection.html\" target=\"_blank\" title=\"[Link opens in a new window]\">data protection statement</a>.");
		}
		
		# Confirmation
		$form->checkboxes (array (
			'name'				=> 'isComplete',
			'title'				=> 'Confirm submission',
			'required'			=> 1,
			'forceAssociative'	=> true,
			'values'			=> array (1 => 'Yes, the submission is now complete. I have checked the above and no further changes are needed.'),
		));
		
		# Final text
		$form->heading ('p', '<strong>Once your abstracts are submitted, no further changes are allowed.<br />Please contact ' . application::encodeEmailAddress (str_replace (',', ' and ', $this->instance['organisationEmail'])) . ' if a further change must be made.</strong>');
		
		# Process the form
		if ($result = $form->process ($html)) {
			
			# Remove the list signup data
			if ($this->instance['listSignup']) {
				if ($result['listSignup']) {
					application::utf8Mail ($this->instance['listSignup'], "subscribe address={$this->userVisibleIdentifier}", '', 'From: ' . $this->userVisibleIdentifier);
				}
				unset ($result['listSignup']);
			}
			
			# Update the data
			$result['isComplete'] = 1;
			$this->databaseConnection->update ($this->settings['database'], 'submissions', $result, array ('id' => $submission['id']));
			
			# Reload the submission to ensure data integrity
			$submission = $this->databaseConnection->selectOne ($this->settings['database'], 'submissions', array ('id' => $submission['id']));
			
			# Show the submission page
			$completedSubmissionHtml = $this->completedSubmissionHtml ($moniker, $submission);
			$html .= $completedSubmissionHtml;
			
			# Get the e-mail of the corresponding author
			$correspondingAuthorId = $submission['correspondingAuthor'];
			$correspondingAuthorEmail = $this->authors[$correspondingAuthorId]['email'];
			
			# Mail the submission
			$to = $this->userVisibleIdentifier;	// #!# A bit brittle?
			$headers = array ();
			$headers[] = "From: {$this->settings['administratorEmail']}";
			if ($correspondingAuthorEmail != $to) {
				$headers[] = "Cc: {$correspondingAuthorEmail}";
			}
			$bcc = array ($this->instance['organisationEmail']);
			$headers[] = 'Bcc: ' . implode (', ', $bcc);
			if ($this->settings['administratorEmail'] != $this->instance['organisationEmail']) {
				$headers[] = "Reply-To: {$this->instance['organisationEmail']}";
			}
			
			# Construct the mail message
			$message  = "\nYou have made a submission as below.";
			if ($correspondingAuthorEmail != $to) {$message .= "\n\nThis message has been copied to the corresponding author.";}
			$message .= "\n\nIf you have any problems, or if changes must be made, please e-mail:\n" . str_replace (',', ' and ', $this->instance['organisationEmail']) . ' .';
			$message .= "\n\nYou can view this submission online at:\n{$_SERVER['_SITE_URL']}{$this->baseUrl}/{$moniker}/{$item}/";
			$message .= "\n\n" . str_repeat ('-', 76);
			$message .= $this->completedSubmissionAsText ($completedSubmissionHtml);
			
			# Send the mail and confirm success status
			$subject = $this->instances[$moniker]['title'] . ": Submission (#{$submission['id']})";
			application::utf8Mail ($to, $subject, wordwrap ($message), implode ("\r\n", $headers));
			
			# Confirm
			$html  = "\n<br />";
			$html .= "\n<div class=\"graybox\">";
			$html .= "\n<p>{$this->tick} <strong>Thank you - your submission has now been submitted. A confirmation has been sent by e-mail to you" . ($correspondingAuthorEmail == $to ? '' : ' and the corresponding author') . '.</strong></p>';
			$html .= "\n</div>";
			
			# Wipe out all tabs for the instance
			$this->instanceTabs = array ();
		}
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to convert a completed submission to text
	private function completedSubmissionAsText ($submission)
	{
		# Add an underline after each <h4></h4>
		$submission = preg_replace_callback ("~<h4>([^<]+)</h4>~", create_function ('$matches', 'return "\n\n\n" . $matches[0] . "\n" . str_repeat ("-", min (75, strlen ($matches[0]) - strlen ("<h4>" . "</h4>")));'), $submission);
		
		# Remove all HTML
		$submission = strip_tags ($submission);
		
		# Unconvert entities <>&
		$submission = htmlspecialchars_decode ($submission);
		
		# Remove whitespace from start of each line
		$submission = preg_replace ("/^[ \t]+/m", '', $submission);
		
		# Return the submission as text
		return $submission;
	}
	
	
	# Function to add an author
	private function submissionAddauthor ($submission, $moniker, $item)
	{
		# Start the HTML
		$html  = '';
		
		# Update the title
		$this->submissionTitle[] = ($this->authors ? 'Add another author' : 'Add an author');
		
		# Run the main part of the form
		$formHtml = '';
		if (!$result = $this->formAuthorSection ($formHtml)) {
			$html .= "<p class=\"tools\"><a href=\"{$this->baseUrl}/{$moniker}/{$item}/\">" . "<img src=\"/images/icons/arrow_undo.png\" alt=\"\" class=\"icon\" /> Cancel adding a new author</a></p>";
			if (!$this->authors) {
				$html .= "<p><img src=\"/images/icons/exclamation.png\" alt=\"Not yet submitted\" class=\"icon\" /> You <strong>must</strong> add the authors in the order in which you want them to appear.</p>";
			}
			$html .= $formHtml;
			return $html;
		}
		
		# Insert fixed data
		$result["submission__JOIN__{$this->settings['database']}__submissions__reserved"] = $submission['id'];
		
		# Add the data
		$this->databaseConnection->insert ($this->settings['database'], 'authors', $result);
		
		# Get the author ID and create a formatted key from it
		$authorId = $this->databaseConnection->getLatestId ();
		
		# Confirm and issue a header
		$location = "{$this->baseUrl}/{$moniker}/{$item}/{$authorId}/";
		$html .= "\n<p>{$this->tick} Now added. <a href=\"{$location}\">View the entry.</a></p>";
		application::sendHeader (301, "{$_SERVER['_SITE_URL']}{$location}");
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create a clone form for an author
	private function authorCloneSelectForm (&$html = '', $data = array ())
	{
		# End if no authors
		if (!$this->authors) {return false;}
		
		# Arrange as an associative list
		$authors = array ();
		$i = 0;
		foreach ($this->authors as $authorId => $author) {
			$i++;
			$authors[$authorId] = "Author #{$i}: {$author['forename']} {$author['surname']}";
		}
		
		# Create the form
		$form = new form (array (
			'displayRestrictions' => false,
			'name' => 'clonefrom',
			'nullText' => false,
			'display'		=> 'template',
			'displayTemplate' => '{[[PROBLEMS]]}<p class="right">' . ($data ? 'Replace with cloned' : 'Copy') . ' details from: {author} {[[SUBMIT]]}</p>',
			'submitButtonText' => 'Go!',
			'submitButtonAccesskey' => false,
			'formCompleteText' => false,
			'requiredFieldIndicator' => false,
		));
		$form->select (array (
			'name'		=> 'author',
			'title'		=> false,
			'values'	=> $authors,
			'required'	=> true,
		));
		if (!$result = $form->process ($html)) {return false;}
		
		# Lookup the details of this author
		$authorId = $result['author'];
		$author = $this->authors[$authorId];
		
		# Return the author details
		return $author;
	}
	
	
	# Function to view an author
	private function submissionViewauthor ($submission, $moniker, $item)
	{
		# Start the HTML
		$html  = '';
		
		# Get the author or end
		if (!$author = $this->getAuthor ($html)) {return false;}
		
		# Start with an edit link
		$html .= "<p class=\"tools\"><a href=\"{$this->baseUrl}/{$moniker}/{$item}/{$author['id']}/edit.html\">" . "<img src=\"/images/icons/pencil.png\" alt=\"\" class=\"icon\" /> Edit this author</a> | <a href=\"{$this->baseUrl}/{$moniker}/{$item}/{$author['id']}/delete.html\">" . "<img src=\"/images/icons/cross.png\" alt=\"\" class=\"icon\" /> Delete author&hellip;</a></p>";
		
		# Update the title
		$this->submissionTitle[] = $this->authorName ($author);
		
		# Add the author table
		$html .= $this->authorTable ($author);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to construct the visible name of an author
	private function authorName ($author)
	{
		return implode (' ', array ($author['title'], $author['forename'], $author['surname']));
	}
	
	
	# Function to create the main table for the submission
	private function authorTable ($author, $cssClass = 'lines')
	{
		# Get the table headings
		$headings = $this->databaseConnection->getHeadings ($this->settings['database'], 'authors');
		
		# Remove internal fields
		unset ($author['id']);
		unset ($author["submission__JOIN__{$this->settings['database']}__submissions__reserved"]);
		
		# Compile the HTML
		$html  = application::htmlTableKeyed ($author, $headings, $omitEmpty = false, $cssClass);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to get and validate an author
	private function getAuthor (&$html)
	{
		# Ensure the author exists and is numeric, not zero, and in the list
		$authorId = (isSet ($_GET['author']) && ctype_digit ($_GET['author']) && ($_GET['author'] > 0) && isSet ($this->authors[$_GET['author']]) ? $_GET['author'] : false);
		if (!$authorId) {
			application::sendHeader (404);
			$html .= "\n<p>The author you requested does not appear to be valid. Please check the URL and try again.</p>";
			return false;
		}
		
		# Assign the author
		$author = $this->authors[$authorId];
		
		# Return the author
		return $author;
	}
	
	
	# Function to edit an author
	private function submissionEditauthor ($submission, $moniker, $item)
	{
		# Start the HTML
		$html  = '';
		
		# Get the author or end
		if (!$author = $this->getAuthor ($html)) {return false;}
		
		# Run the main part of the form
		$formHtml = '';
		if (!$result = $this->formAuthorSection ($formHtml, $author)) {
			$html .= "<p class=\"tools\"><a href=\"{$this->baseUrl}/{$moniker}/{$item}/{$author['id']}/\">" . "<img src=\"/images/icons/arrow_undo.png\" alt=\"\" class=\"icon\" /> Cancel editing</a></p>";
			$html .= $formHtml;
			return $html;
		}
		
		# Update the data
		$this->databaseConnection->update ($this->settings['database'], 'authors', $result, array ('id' => $author['id']));
		
		# Reassign the submission title
		$this->submissionTitle[] = $result['title'];
		
		# Confirm and issue a header
		$location = "{$this->baseUrl}/{$moniker}/{$item}/{$author['id']}/";
		$html .= "\n<p>{$this->tick} Now updated. <a href=\"{$location}\">View the updated entry.</a></p>";
		application::sendHeader (301, "{$_SERVER['_SITE_URL']}{$location}");
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to edit a submission
	private function submissionDeleteauthor ($submission, $moniker, $item)
	{
		# Start the HTML
		$html  = '';
		
		# Get the author or end
		if (!$author = $this->getAuthor ($html)) {return false;}
		
		# Load and create a form
		require_once ('ultimateForm.php');
		$form = new form (array (
			'databaseConnection' => $this->databaseConnection,
			'formCompleteText' => false,
			'nullText' => '',
			'display' => 'paragraphs',
			'submitButtonText' => 'Delete author and continue',
		));
		
		# Form text/widgets
		$form->heading ('p', "<strong>Do you really want to delete this author?</strong> This cannot be undone.");
		$form->select (array (
			'name'				=> 'confirmation',
			'title'				=> 'Confirm deletion',
			'required'			=> 1,
			'forceAssociative'	=> true,
			'values'			=> array ('Yes, delete this author permanently'),
		));
		
		# Process the form
		if (!$result = $form->process ($html)) {
			$html .= $this->authorTable ($author, 'lines compressed');
			return $html;
		}
		
		# Delete the authors and the submission
		$this->databaseConnection->delete ($this->settings['database'], 'authors', array ('id' => $author['id']));
		
		# Confirm deletion
		$html .= "\n<p>The author has been deleted.</p>";
		
		# Delete this author's tab
		unset ($this->instanceTabs["{$this->baseUrl}/{$moniker}/{$item}/{$author['id']}/"]);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to get the authors for a submission
	private function getAuthors ($submissionId)
	{
		# Get the data
		$data = $this->databaseConnection->select ($this->settings['database'], 'authors', array ("submission__JOIN__{$this->settings['database']}__submissions__reserved" => $submissionId), array (), true, 'id');
		
		# Return the data
		return $data;
	}
	
	
	# Function to provide auto-complete functionality
	public function data ()
	{
		# End if no query or no field
		if (!isSet ($_GET['term']) || !strlen ($_GET['term'])) {return false;}
		
		# Obtain the query and the field
		$term = $_GET['term'];
		
		# Match the data
		$query = "SELECT
			value, label
			FROM {$this->settings['database']}.countries
			WHERE label LIKE :term
			ORDER BY label
			;";
		if (!$data = $this->databaseConnection->getData ($query, false, true, array ('term' => '%' . $_GET['term'] . '%'))) {return false;}
		
		# Arrange the data
		$json = json_encode ($data);
		
		# Send the text
		echo $json;
	}
	
	
	# Admin editing section, substantially delegated to the sinenomine editing component
	public function editing ($attributes = array (), $deny = false, $sinenomineExtraSettings = array ())
	{
		# Define sinenomine attributes
		$attributes = array (
			array ($this->settings['database'], 'instances', 'moniker', array ('regexp' => '^[-a-z0-9]+$')),
			array ($this->settings['database'], 'instances', 'organisationEmail', array ('multiple' => true)),
			array ($this->settings['database'], 'instances', 'dataProtectionHtml', array ('editorBasePath' => $this->baseUrl . '/_ckeditor/', 'editorFileBrowser' => false, 'editorToolbarSet' => 'BasicLonger', )),
		);
		
		# Define tables to deny editing for
		$deny[$this->settings['database']] = array (
			'administrators',
			'countries',
			// 'users',
		);
		
		# Hand off to the default editor, which will echo the HTML
		parent::editing ($attributes, $deny);
	}
	
	
	# Data protection page (instance-specific)
	public function dataprotection ($instanceId)
	{
		# Define the HTML
		if ($this->instances[$instanceId]['dataProtectionHtml']) {
			$html  = $this->instances[$instanceId]['dataProtectionHtml'];
		} else {
			$html  = "<p>No data protection statement is available. Please <a href=\"{$this->baseUrl}/feedback.html\">contact us</a> if you have any queries.</p>";
		}
		
		# Show the HTML
		echo $html;
	}
	
	
	# Download page
	public function download ()
	{
		# Start the HTML
		$html  = '';
		
		# Get the total submissions for each instance; see http://stackoverflow.com/questions/1313819
		$submissions = array ();
		foreach ($this->instances as $moniker => $instance) {
			$instanceId = $instance['id'];
			$limitToInstanceSubclause = "instance__JOIN__{$this->settings['database']}__instances__reserved = {$instanceId}";
			$submissions[$moniker]['complete']   = $this->databaseConnection->getTotal ($this->settings['database'], 'submissions', "WHERE {$limitToInstanceSubclause} AND isComplete = 1");
			$submissions[$moniker]['incomplete'] = $this->databaseConnection->getTotal ($this->settings['database'], 'submissions', "WHERE {$limitToInstanceSubclause} AND (isComplete != 1 or isComplete IS NULL)");
			
			# Get a list of incomplete users
			$query = "SELECT
					CONCAT(users.email, ' (#', submissions.id, ')') AS submission
				FROM submissions
				LEFT JOIN users ON submissions.user__JOIN__{$this->settings['database']}__users__reserved = users.id
				WHERE {$limitToInstanceSubclause} AND (isComplete != 1 or isComplete IS NULL)
				ORDER BY email
			;";
			$submissions[$moniker]['incompleteusers'] = $this->databaseConnection->getPairs ($query);
		}
		
		# Create a list
		$list = array ();
		foreach ($this->instances as $moniker => $instance) {
			$list[$moniker] = "<a href=\"{$this->baseUrl}/{$moniker}/{$moniker}.csv\"><strong>{$moniker}.csv</strong></a> <strong>(" . htmlspecialchars ($instance['title']) . ")</strong> - {$submissions[$moniker]['complete']} complete submissions" . ($submissions[$moniker]['incomplete'] ? "; excludes {$submissions[$moniker]['incomplete']} incomplete " . ($submissions[$moniker]['incomplete'] == 1 ? 'submission' : 'submissions') . " from: <br />" . application::htmlUl ($submissions[$moniker]['incompleteusers'], 0, 'small compact') : '');
		}
		
		# Compile the HTML
		$html .= "\n<p>You can download the data as a CSV file below:</p>";
		
		# Give a warning about Excel's terrible handling in UTF-8 in CSV files
		$html .= "\n<p class=\"warning\"><strong>Warning:</strong> Microsoft Excel tends to mangle international characters in CSV files. You are strongly recommended to open the CSV files above using <strong>OpenOffice</strong> instead, and select '<strong>Unicode (UTF-8)</strong>' as the character encoding when opening. You can then save the file in the '<strong>Microsoft Excel 97/2000/XP (.xls)</strong>' format, which will preserve the Unicode encoding. Then close (and discard) the CSV file and open the new .xls file using Excel.</p>";
		
		# Show the list
		$html .= application::htmlUl ($list);
		
		# Show the HTML
		echo $html;
	}
	
	
	# CSV download
	public function downloadcsv ($moniker)
	{
		# Ensure the requested ID exists
		if (!isSet ($this->instances[$moniker])) {
			$this->page404 ();
			return false;
		}
		
		# Get the data for this instance
		$instance = $this->instances[$moniker];
		
		# Get the main submissions
		$conditions = array ("instance__JOIN__{$this->settings['database']}__instances__reserved" => $instance['id'], 'isComplete' => 1, );
		if (!$submissions = $this->databaseConnection->select ($this->settings['database'], 'submissions', $conditions, array (), true, $orderBy = 'id')) {
			echo "<p>No completed submissions so far.</p>";
			return false;
		}
		
		# Create a list of submission IDs
		$submissionIds = array_keys ($submissions);
		
		# Get the author entries; note that ORDER BY id is important as that maintains the entered order
		$query = "SELECT * FROM authors WHERE submission__JOIN__{$this->settings['database']}__submissions__reserved IN (" . implode (', ', $submissionIds) . ') ORDER BY id;';
		if (!$authors = $this->databaseConnection->getData ($query, "{$this->settings['database']}.submissions")) {
			echo "<p>No authors found.</p>";
			#!# Report error - this should not be possible if there are completed submissions
			return false;
		}
		
		# Cache the fieldnames that represent the structure of an author
		$listHavingFirstAuthorOnly = array_slice ($authors, 0, 1);
		$authorFieldnames = array_keys ($listHavingFirstAuthorOnly[0]);
		
		# Regroup the author entries by id
		$authors = application::regroup ($authors, "submission__JOIN__{$this->settings['database']}__submissions__reserved", false);
		
		# Determine the maximum number of authors that any submission has
		$counts = array ();
		foreach ($authors as $submissionId => $authorSet) {
			$counts[$submissionId] = count ($authorSet);
		}
		$slots = max ($counts);
		
		# Get the users data
		$query = "SELECT id,email FROM {$this->settings['database']}.users;";
		$users = $this->databaseConnection->getPairs ($query);
		
		// application::dumpData ($instance);
		// application::dumpData ($submissions);
		// application::dumpData ($authors);
		// application::dumpData ($users);
		
		# Start an array which will be used to build a simple table
		$data = array ();
		foreach ($submissions as $submissionId => $submission) {
			
			# Start by adding all the fields to this entry
			$data[$submissionId] = $submission;
			
			# For this submission, index all the authors from 0 by resetting the keys
			$authors[$submissionId] = array_values ($authors[$submissionId]);
			
			# For each available author slot, create a prefix (author1, author2, etc.) indexed from 1 rather than 0
			for ($slot = 0; $slot < $slots; $slot++) {
				$slot1Indexed = $slot + 1;
				$prefix = "author{$slot1Indexed}_";
				
				# If an author exists in this slot, add each of its values into the main entry
				if (isSet ($authors[$submissionId][$slot])) {
					foreach ($authors[$submissionId][$slot] as $authorKey => $authorValue) {
						$fieldname = $prefix . $authorKey;
						$data[$submissionId][$fieldname] = $authorValue;
					}
					
				# If an author doesn't exist
				} else {
					foreach ($authorFieldnames as $authorKey) {
						$fieldname = $prefix . $authorKey;
						$data[$submissionId][$fieldname] = NULL;
					}
				}
				
				# Remove the JOIN field
				$joinField = $prefix . "submission__JOIN__{$this->settings['database']}__submissions__reserved";
				unset ($data[$submissionId][$joinField]);
			}
			
			# Replace internal fields with more useful info
			$data[$submissionId]["instance__JOIN__{$this->settings['database']}__instances__reserved"] = $instance['moniker'];
			$data[$submissionId]["user__JOIN__{$this->settings['database']}__users__reserved"] = $users[$data[$submissionId]["user__JOIN__{$this->settings['database']}__users__reserved"]];
		}
		
		# Show data dump if required
		if (isSet ($_GET['data'])) {
			application::dumpData ($data);
			return true;
		}
		
		# Serve as CSV
		require_once ('csv.php');
		csv::serve ($data, $moniker);
	}
}

?>
