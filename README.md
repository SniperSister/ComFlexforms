# FlexForms

FlexForms is a form extension built by developers, for developers. It:

* is build for Joomla 3.x
* is based on F0F (you need to install it, it's not the included FOF within the Joomla! Core)
* uses JForm XML files for the form building and validation
* has a per-form layout override system to allow individual styles
* triggers the Joomla plugin system to allow special tasks when submitting a form
* it offers per-form language files for multilingual forms
* sends, as an option, email to administrators and form submitters
* attaches, as an option, uploaded files to these emails

## Getting started

### Install FlexForms
No rocket science required - just install it using the normal Joomla installer.

### Define your forms
You are a developer. A genius! And as a genius, you don't need a fancy GUI in order to create a simple form.

Instead, you're using the good old Joomla! JForm XML syntax to define your form, and save it in

    /media/com_flexforms/forms/FORMNAME.xml
  
So, for example to get a dead easy contact form, you create a file called "contact.xml" with the following XML tree:

```xml
<?xml version="1.0" encoding="utf-8"?>
<form>
    <fieldset>
        <field name="title" type="list" required="true" size="1" label="Title">
            <option value="">- Select -</option>
            <option value="Mr">Mr</option>
            <option value="Mrs">Mrs</option>
        </field>

        <field name="firstname" type="text" required="true" label="Firstname" />

        <field name="lastname" type="text" required="true" label="Lastname" />

        <field name="message" type="textarea" cols="50" rows="5" label="YOur Message" />
    </fieldset>
</form>
```

### Configure your form
In the backend, navigate to "Components->FlexForms", hit "New" and configure your form:

 * **Title**: that's the human readable form title that is, by default, also used as the heading
 * **Status**: self explaining, huh?
 * **Layout**: select the layout that is used to display your form - see below for a more detailed explanation
 * **Form**: select the XML file that contains your form definition
 * **Send mail to admin**: select if an email with the submitted form should be sent to the administrator(s)
 * **Administrators**: provide one or more email addresses that should receive the form; comma-separate multiple addresses
 * **Admin-mail subject**: self explaining
 * **Admin-mail text**: define the mail body. You can use placeholders that follow the pattern {LOWERCASEFIELDNAME}
 * **Admin-mail attachments**: attach uploaded files to the admin mail
 * **Send mail to user**: select if an email should be send to the user who has submitted the form
 * **User emailfield**: enter the fieldname of the field, where the user has entered his emailaddress
 * **User-mail subject, text, attachments**: see above
 
Afterwards, hit save, create a menu item and you're ready to go!
 
 
## Advanced stuff aka "the fun part"

### Using layout overrides
This is where the fun part starts! With FlexForms, you have the possibilty to create per-form layout overrides! This gives you endless flexibility and was my original motivation to write FlexForms.
 
You have three choices:

 1. use the default layout provided by the component.
 2. use a form specific layout file that is saved in the same folder as the XML. To do so, create your layout (by creating a copy of the default component layout) and save it as FORMNAME.php in the /media/com_flexforms/forms directory. Afterwards, select "Media directory" as layout option in the form parameters.
 3. use a custom, form-independent layout. To do so, create your layout and save it in the html/com_flexforms/form directory of your template. Afterwards, select it in the form parameters.
 
### Using language files
You can create per-form language files to create multi-language forms. Save your language strings in an INI-file named com_flexforms.FORMNAME.ini in the /media/com_flexforms/language/{LANG}/ directory.

### Using plugins
FlexForms triggers a ton of plugin events during execution. To make use of these triggers, create a new plugin, add it to the "flexforms" plugin group and use one of the following triggers:

 * onBeforeFlexformsReturnForm
 * onBeforeFlexformsValidate
 * onAfterFlexformsValidate
 * onBeforeFlexformsSendOwnerMail
 * onBeforeFlexformsSendSenderMail
 * onAfterFlexformsSubmit
 