<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjucm
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2018 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('jquery.token');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_tjucm', JPATH_SITE);
$doc = JFactory::getDocument();
$doc->addScript(JUri::root() . 'administrator/components/com_tjucm/assets/js/jquery.form.js');
$doc->addScript(JUri::root() . 'administrator/components/com_tjucm/assets/js/itemform.js');
$doc->addScript(JUri::root() . 'administrator/components/com_tjfields/assets/js/tjfields.js');
JHtml::_('stylesheet', 'administrator/components/com_tjucm/assets/css/tjucm.css');

/*
 * Script to show alert box if form changes are made and user is closing/refreshing/navigating the tab
 * without saving the content
 */
$doc->addScript(JUri::root() . 'administrator/components/com_tjucm/assets/js/jquery.are-you-sure.js');

/*
 * Script to show alert box if form changes are made and user is closing/refreshing/navigating the tab
 * without saving the content on iphone|ipad|ipod|opera
 */
$doc->addScript(JUri::root() . 'administrator/components/com_tjucm/assets/js/ays-beforeunload-shim.js');

$jinput                    = JFactory::getApplication();
$editRecordId              = $jinput->input->get("id", '', 'INT');
$baseUrl                   = $jinput->input->server->get('REQUEST_URI', '', 'STRING');
$calledFrom                = (strpos($baseUrl, 'administrator')) ? 'backend' : 'frontend';
$layout                    = ($calledFrom == 'frontend') ? 'default' : 'edit';
$fieldsets_counter_deafult = 0;
$setnavigation             = false;

if ($this->item->id)
{
	$itemState = ($this->item->draft && ($this->allow_auto_save || $this->allow_draft_save)) ? 1 : 0;
}
else
{
	$itemState = ($this->allow_auto_save || $this->allow_draft_save) ? 1 : 0;
}
?>
<script type="text/javascript">

	/* Code to show alert box if form changes are made and user is closing/refreshing/navigating the tab
	 * without saving the content
	 */
	jQuery(function() {
		jQuery('#item-form').areYouSure();
	});

	jQuery(window).load(function ()
	{
		jQuery('#item-form .nav-tabs li a').first().click();
	});

	Joomla.submitbutton = function (task)
	{
		if (task == 'itemform.cancel')
		{
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else
		{
			if (task != 'itemform.cancel' && document.formvalidator.isValid(document.id('item-form')))
			{
				Joomla.submitform(task, document.getElementById('item-form'));
			}
			else
			{
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php');?>" method="post" enctype="multipart/form-data" name="adminForm" id="item-form" class="form-validate">
	<?php
	if ($this->allow_auto_save == '1')
	{
	?>
	<div class="alert alert-info" style="display:none;" id="tjucm-auto-save-disabled-msg">
		<a class="close" data-dismiss="alert">×</a>
		<div class="msg">
			<div>
			<?php echo JText::_("COM_TJUCM_MSG_FOR_AUTOSAVE_FEATURE_DISABLED"); ?>
			</div>
		</div>
	</div>
	<?php
	}
	?>
	<div>
		<fieldset>
			<input type="hidden" name="jform[id]" id="recordId" value="<?php echo $editRecordId; ?>" />
			<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
			<input type="hidden" name="jform[state]" value="<?php echo $this->item->state;?>" />
			<input type="hidden" name="jform[client]" value="<?php echo $this->client;?>" />
			<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
			<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
			<input type="hidden" name="itemState" id="itemState" value="<?php echo $itemState; ?>"/>
			<?php echo $this->form->renderField('created_by'); ?>
			<?php echo $this->form->renderField('created_date'); ?>
			<?php echo $this->form->renderField('modified_by'); ?>
			<?php echo $this->form->renderField('modified_date'); ?>
		</fieldset>
	</div>
	<?php
	if ($this->form_extra)
	{
		?>
		<div class="form-horizontal">
		<?php
		// Code to display the form
		echo $this->loadTemplate('extrafields');
		?>
		</div>
		<?php
	}

	if ($editRecordId)
	{
	?>
	<div class="alert alert-success" style="display: block;">
		<a class="close" data-dismiss="alert">×</a>
		<div class="msg">
			<div>
			<?php echo JText::_("COM_TJUCM_NOTE_ON_FORM"); ?>
			</div>
		</div>
	</div>
	<?php
	}
	?>
	<div id="draft_msg" class="alert alert-success" style="display: none;">
		<a class="close" data-dismiss="alert">×</a>
		<?php echo JText::_("COM_TJUCM_MSG_ON_DRAFT_FORM"); ?>
	</div>

	<div class="form-actions">
		<?php
		// Show next previous buttons only when there are mulitple tabs/groups present under that field type
		$fieldArray = $this->form_extra;

		foreach ($fieldArray->getFieldsets() as $fieldName => $fieldset)
		{
			if (count($fieldArray->getFieldsets()) > 1)
			{
				$setnavigation = true;
			}
		}

		if (isset($setnavigation) && $setnavigation == true)
		{
			if (!empty($this->allow_draft_save))
			{
				?>
				<button type="button" class="btn btn-primary" id="previous_button" >
					<i class="icon-arrow-left-2"></i>
					<?php echo JText::_('COM_TJUCM_PREVIOUS_BUTTON'); ?>
				</button>
				<button type="button" class="btn btn-primary" id="next_button" >
					<?php echo JText::_('COM_TJUCM_NEXT_BUTTON'); ?>
					<i class="icon-arrow-right-2"></i>
				</button>
				<?php
			}
		}

		if ($calledFrom == 'frontend')
		{
			if (($this->allow_auto_save || $this->allow_draft_save) && $itemState)
			{
				?>
				<input type="button" class="btn btn-width150 br-0 btn-default font-normal" id="tjUcmSectionDraftSave"
				value="<?php echo JText::_("COM_TJUCM_SAVE_AS_DRAFT_ITEM"); ?>"
				onclick="tjUcmItemForm.saveUcmFormData();" />
				<?php
			}
			?>
			<input type="button" class="btn btn-success" value="<?php echo JText::_("COM_TJUCM_SAVE_ITEM"); ?>"
			id="tjUcmSectionFinalSave" onclick="tjUcmItemForm.saveUcmFormData();" />
			<?php
		}
		?>
	</div>
	<input type="hidden" name="layout" value="<?php echo $layout ?>"/>
	<input type="hidden" name="task" value="itemform.save"/>
	<input type="hidden" name="form_status" id="form_status" value=""/>
	<input type="hidden" name="tjucm-autosave" id="tjucm-autosave" value="<?php echo $this->allow_auto_save;?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
