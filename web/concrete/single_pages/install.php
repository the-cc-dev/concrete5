<? defined('C5_EXECUTE') or die("Access Denied."); ?> 

<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery.tipsy.js"></script>
<script type="text/javascript">
$(function() {
	$(".tooltip").tipsy();
});
</script>

<? 

$introMsg = t('To install concrete5, please fill out the form below.');

if (isset($successMessage)) { ?>

<script type="text/javascript">
$(function() {
	$( "#install-progress-bar" ).progressbar({
		value: 0
	});
	
<? for ($i = 1; $i <= count($installRoutines); $i++) {
	$routine = $installRoutines[$i-1]; ?>

	ccm_installRoutine<?=$i?> = function() {
		<? if ($routine->getText() != '') { ?>
			$("#install-progress-summary").html('<?=$routine->getText()?>');
		<? } ?>
		$.getJSON('<?=$this->url("/install", "run_routine", $installPackage, $routine->getMethod())?>', function(r) {
			if (r.error) {
				$("#install-progress-wrapper").hide();
				$("#install-progress-errors").append('<div class="alert-message error">' + r.message + '</div>');
				$("#install-progress-error-wrapper").fadeIn(300);
			} else {
				$( "#install-progress-bar" ).progressbar({
					value: <?=$routine->getProgress()?>
				});
				<? if ($i < count($installRoutines)) { ?>
					ccm_installRoutine<?=$i+1?>();
				<? } else { ?>
					$("#install-progress-wrapper").fadeOut(300, function() {
						$("#success-message").fadeIn(300);
					});
				<? } ?>
			}
		});
	}
	
<? } ?>

	ccm_installRoutine1();

});

</script>


<h1><?=t('Install concrete5')?></h1>

<div class="ccm-install-intro">
<div id="success-message">
<?=$successMessage?>
<br/><br/>
<a href="<?=DIR_REL?>/"><?=t('Continue to your site.')?> &gt;</a>
</div>

<div id="install-progress-wrapper">

<div id="install-progress-summary">
<?=t('Beginning Installation')?>
</div>

<div id="install-progress-bar"></div>

</div>

<div id="install-progress-error-wrapper">

<div id="install-progress-errors"></div>
<div id="install-progress-back">
<input type="button" class="btn" onclick="window.location.href='<?=$this->url('/install')?>'" value="<?=t('Back')?>" />
</div>

</div>

</div>

<? } else if ($this->controller->getTask() == 'setup' || $this->controller->getTask() == 'configure') { ?>

<div class="page-header">
<h1><?=t('Install concrete5')?></h1>
</div>

<form action="<?=$this->url('/install', 'configure')?>" method="post">

<div class="row">
<div class="span8 columns">

	<input type="hidden" name="locale" value="<?=$locale?>" />
	
	<fieldset>
		<legend><?=t('Site Information')?></legend>
		<div class="clearfix">
		<label for="SITE"><?=t('Name Your Site')?>:</label>
		<div class="input">
			<?=$form->text('SITE', array('class' => 'xlarge'))?>
		</div>
		</div>
			
	</fieldset>
	
	<fieldset>
		<legend><?=t('Administrator Information')?></legend>
		<div class="clearfix">
		<label for="uEmail"><?=t('Email Address')?>:</label>
		<div class="input">
			<?=$form->email('uEmail', array('class' => 'xlarge'))?>
		</div>
		</div>
		<div class="clearfix">
		<label for="uPassword"><?=t('Password')?>:</label>
		<div class="input">
			<?=$form->text('uPassword', array('class' => 'xlarge'))?>
		</div>
		</div>
		<div class="clearfix">
		<label for="uPasswordConfirm"><?=t('Confirm Password')?>:</label>
		<div class="input">
			<?=$form->text('uPasswordConfirm', array('class' => 'xlarge'))?>
		</div>
		</div>
		
	</fieldset>

</div>
<div class="span8 columns">

	<fieldset>
		<legend><?=t('Database Information')?></legend>

	<div class="clearfix">
	<label for="DB_SERVER"><?=t('Server')?>:</label>
	<div class="input">
		<?=$form->text('DB_SERVER', array('class' => 'xlarge'))?>
	</div>
	</div>

	<div class="clearfix">
	<label for="DB_USERNAME"><?=t('MySQL Username')?>:</label>
	<div class="input">
		<?=$form->text('DB_USERNAME', array('class' => 'xlarge'))?>
	</div>
	</div>

	<div class="clearfix">
	<label for="DB_PASSWORD"><?=t('MySQL Password')?>:</label>
	<div class="input">
		<?=$form->text('DB_PASSWORD', array('class' => 'xlarge'))?>
	</div>
	</div>

	<div class="clearfix">
	<label for="DB_DATABASE"><?=t('Database Name')?>:</label>
	<div class="input">
		<?=$form->text('DB_DATABASE', array('class' => 'xlarge'))?>
	</div>
	</div>
</div>
</div>

<div class="row">
<div class="span16 columns">

	<fieldset>
	<legend><?=t("Starting Point")?></legend>
	<div class="clearfix">
		<label><?=t('Sample Content')?></label>
		<div class="input">
		asdf
		</div>
	</div>
	</fieldset>
	
	</div>
</div>

<div class="well">
	<?=$form->submit('submit', t('Install concrete5'), array('class' => 'large primary'))?>
</div>
</form>

<? } else if (isset($locale) || count($locales) == 0) { ?>

<script type="text/javascript">

$(function() {
	$("#install-errors").hide();
});

<? if ($this->controller->passedRequiredItems()) { ?>
	var showFormOnTestCompletion = true;
<? } else { ?>
	var showFormOnTestCompletion = false;
<? } ?>


$(function() {
	$(".ccm-test-js img").hide();
	$("#ccm-test-js-success").show();
	$("#ccm-test-request-loading").ajaxError(function(event, request, settings) {
		$(this).attr('src', '<?=ASSETS_URL_IMAGES?>/icons/error.png');
		$("#ccm-test-request-tooltip").show();
	});
	$.getJSON('<?=$this->url("/install", "test_url", "20", "20")?>', function(json) {
		// test url takes two numbers and adds them together. Basically we just need to make sure that
		// our url() syntax works - we do this by sending a test url call to the server when we're certain 
		// of what the output will be
		if (json.response == 40) {
			$("#ccm-test-request-loading").attr('src', '<?=ASSETS_URL_IMAGES?>/icons/success.png');
			if (showFormOnTestCompletion) {
				$("#install-success").show();
			} else {
				$("#install-errors").show();
			}
		} else {
			$("#ccm-test-request-loading").attr('src', '<?=ASSETS_URL_IMAGES?>/icons/error.png');
			$("#ccm-test-request-tooltip").show();
			$("#install-errors").show();
		}
	});
	
});
</script>


<div class="page-header">
	<h1><?=t('Install concrete5')?></h1>
</div>


<h3><?=t('Testing Required Items')?></h3>
<div class="row">
<div class="span8 columns">

<table class="zebra-striped">
<thead>
<tr>
	<th></th>
	<th colspan="2"><?=t('Item')?></th>
</tr>
</thead>
<tbody>
<tr>
	<td><? if ($phpVtest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/warning.png" /><? } ?></td>
	<td width="100%"><?=t('PHP 5.2')?></td>
	<td><? if (!$phpVtest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="tooltip" title="<?=t('While concrete5 will mostly run on PHP 5.1, 5.2 is strongly encouraged and some functions will not work properly without it.')?>" /><? } ?></td>
</tr>
<tr>
	<td class="ccm-test-js"><img id="ccm-test-js-success" src="<?=ASSETS_URL_IMAGES?>/icons/success.png" style="display: none" />
	<img src="<?=ASSETS_URL_IMAGES?>/icons/error.png" /></td>
	<td width="100%"><?=t('JavaScript Enabled')?></td>
	<td class="ccm-test-js"><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="tooltip" title="<?=t('Please enable JavaScript in your browser.')?>" /></td>
</tr>
<tr>
	<td><? if ($mysqlTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/error.png" /><? } ?></td>
	<td width="100%"><?=t('MySQL Available')?>
	</td>
	<td><? if (!$mysqlTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="tooltip" title="<?=$this->controller->getDBErrorMsg()?>" /><? } ?></td>
</tr>
<tr>
	<td><img id="ccm-test-request-loading"  src="<?=ASSETS_URL_IMAGES?>/dashboard/sitemap/loading.gif" /></td>
	<td width="100%"><?=t('Supports concrete5 request URLs')?>
	</td>
	<td><img id="ccm-test-request-tooltip" src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="tooltip" title="<?=t('concrete5 cannot parse the PATH_INFO or ORIG_PATH_INFO information provided by your server.')?>" /></td>
</tr>
</table>

</div>
<div class="span8 columns">

<table class="zebra-striped">
<thead>
<tr>
	<th></th>
	<th colspan="2"><?=t('Item')?></th>
</tr>
</thead>

<tr>
	<td><? if ($imageTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/error.png" /><? } ?></td>
	<td width="100%"><?=t('Image Manipulation Available')?>
	</td>
	<td><? if (!$imageTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="tooltip" title="<?=t('concrete5 requires GD library 2.0.1 or greater')?>" /><? } ?></td>
</tr>
<tr>
	<td><? if ($xmlTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/error.png" /><? } ?></td>
	<td width="100%"><?=t('XML Support')?>
	</td>
	<td><? if (!$xmlTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="tooltip" title="<?=t('concrete5 requires PHP XML Parser and SimpleXML extensions')?>" /><? } ?></td>
</tr>
<tr>
	<td><? if ($fileWriteTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/error.png" /><? } ?></td>
	<td width="100%"><?=t('Web Server Access to Files and Configuration Directories')?>
	</td>
	<td><? if (!$fileWriteTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="tooltip" title="<?=$t('The config/, packages/ and files/ directories must be writable by your web server.')?>" /><? } ?></td>
</tr>

</tbody>
</table>

</div>
</div>


<h3><?=t('Testing Optional Items')?></h3>

<div class="row">
<div class="span8 columns">


<table class="zebra-striped">
<thead>
<tr>
	<th></th>
	<th><?=t('Item')?></th>
</tr>
</thead>
<tbody>
<tr>
	<td><? if ($remoteFileUploadTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/warning.png" /><? } ?></td>
	<td width="100%"><?=t('Remote File Importing Available')?>
	</td>
	<td><? if (!$remoteFileUploadTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="tooltip" title="<?=t('Remote file importing through the file manager requires the iconv PHP extension.')?>" /><? } ?></td>
</tr>
</table>

</div>
<div class="span8 columns">
<table class="zebra-striped">
<thead>
<tr>
	<th></th>
	<th colspan="2"><?=t('Item')?></th>
</tr>
</thead>

<tr>
	<td><? if ($diffTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/success.png" /><? } else { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/warning.png" /><? } ?></td>
	<td width="100%"><?=t('Version Comparison Available')?>
	</td>
	<td><? if (!$diffTest) { ?><img src="<?=ASSETS_URL_IMAGES?>/icons/tooltip.png" class="tooltip" title="<?=t('You must chmod 755 %s and disable PHP safe mode.', 'concrete/libraries/3rdparty/htmldiff.py')?>" /><? } ?></td>
</tr>
</tbody>
</table>
</div>
</div>

<div class="well" id="install-success">
	<form method="post" action="<?=$this->url('/install','setup')?>">
	<input type="hidden" name="locale" value="<?=$locale?>" />
	<?=$form->submit('install', t('Continue to Installation'), array('class' => 'large primary'))?>
	</form>
</div>

<div class="block-message alert-message error" id="install-errors">
	<p><?=t('There are problems with your installation environment. Please correct them and click the button below to re-run the pre-installation tests.')?></p>
	<div class="block-actions">
	<form method="post" action="<?=$this->url('/install')?>">
	<input type="hidden" name="locale" value="<?=$locale?>" />
	<?=$form->submit('rerun', t('Run Tests'), array('class' => 'small'))?>
	</form>
	</div>	
</div>

<div class="block-message alert-message info">
<?=t('Having trouble? Check the <a href="%s">installation help forums</a>, or <a href="%s">have us host a copy</a> for you.', 'http://www.concrete5.org/community/forums/installation', 'http://www.getconcrete5.com')?>
</div>

<? } else { ?>


<div class="page-header">
	<h1><?=t('Install concrete5')?></h1>
</div>

<div id="ccm-install-intro">

<form method="post" action="<?=$this->url('/install', 'select_language')?>">
<fieldset>
	<div class="clearfix">
	
	<label for="locale"><?=t('Language')?></label>
	<div class="input">
		<?=$form->select('locale', $locales, 'en_US'); ?>
	</div>
	
	</div>
	
	<div class="actions">
	<?=$form->submit('submit', t('Choose Language'))?>
	</div>
</fieldset>
</form>

</div>

<? } ?>