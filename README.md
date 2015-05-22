
Fredi - Friendly Frontend Editing for ProcessWire
=================================================

(c) Antti Peisa 2013-2015


Introduction
-------------------------------------------------

Fredi will bring quick edit links to frontend. Developer can freely decide where
she will output the links and how to style them. Edit link can be for a single field
or for multiple fields together.

Clicking edit links will open modal view to quickly edit and save fields. After
saving it will reload the page and content editor will see his changes immediatly.

Since Fredi will integrate on frontend (though only for admins), I wanted to keep it
as lightweight as possible. There is no javascript library dependencies and only few
lines of css required.


Installation
-------------------------------------------------

Extract /Fredi/ directory under your /site/modules/ directory and install the
Fredi.module. It will automatically install the required FrediProcess.module.

Edit your template file(s) and load fredi there using this line:

```php
$fredi = $modules->get("Fredi");
```

Best place for above line depends on how you are using your template files. But
for basic demo site that PW ships with the best place would be right in the start
of head.inc file.

Other thing you need to do is to add required JS and CSS to your html. Two files that
are required are /Fredi/modal/modal.js and /Fredi/modal/modal.css. Easiest way to do
that is load them using this code:

```php
echo $fredi->renderScript();
```

Good place to add above line is inside your head-tags. Body is fine too, but it
should be loaded before the first edit link you want to provide. There isn't any 3rd
party code dependencies, so it doesn't matter if you have loaded any other scripts or
not.

That's it, now you are ready for Fredi!


How to use
-------------------------------------------------

Using Fredi is very simple and you can easily implement it after you have already
finished your site. So it doesn't force you to build your site in any special way.


### Most simple example of Fredi is this:

Before:
```php
echo $page->body;
```

After:
```php
echo $fredi->body;
echo $page->body;
```

It will show "edit" link right before your body field. Link will be shown only for
logged in users that have rights to edit that page (or to be precise, that field).

On version 1.1.0 and up, Fredi links will be automatically styled and positioned in
relation of the parent element. You can of course tweak the styling with your own
css.


### Editing multiple fields

If you provide edit links for each of your fields, certain templates might get pretty
crowded. Also certain fields are meant to go together. This is how you can feed Fredi
with multiple fields:

```php
echo $fredi->render("headline|title|summary");
```


### Editing all the fields

If you want to show all the fields, you can achieve that with renderAll method:

```php
echo $fredi->renderAll();
```


### Setting page context

One thing we all love in ProcessWire is the ability to easily use content from other
pages. Often we use content from pages that are not show on editor at all. Page context
allows you to add edit links for other pages that the currently viewed one.


#### Setting page context for single field

```php
echo $fredi->body($another_page);
```


#### Setting page context for multiple fields

```php
echo $fredi->render("headline|title|images|body", $another_page);
```

### Setting custom text and classes for edit links

Fields and page context are special settings in Fredi, since those are set per edit link.
All the other settings can be given through setter methods. Currently there is only one
setting available and it gives possibility to overwrite the default edit text. These
setter methods all return the $fredi object back, so you can chain these nicely.

```php
echo $fredi->setText("Edit bodytext")->body;
// Or
echo $fredi->setText("Manage footer images")->images($footer_settings_page);
// Or
echo $fredi->setText("Edit press release details")->render("title|summary|publishdate", $another_page);
```

Fredi has good memory, so when you once setText, it will remember it. You can always set
new text or even reset to default.

You can also add additional css classes for link element:
```php
echo $fredi->setClass("featured")->body;
// Or
echo $fredi->setClass("featured small")->body;
```


### Hiding tabs

In context of inline editing, the additional tabs (Children, Settings and Delete) might be
undesirable. You can edit ProcessFredi settings to globally disable those from Fredi modals.
Other option is to disable them case by case. It works in similar manner like setText above:

```php
echo $fredi->hideTabs("children");
// Or
echo $fredi->hideTabs("children|delete|settings");
```

### Reset settings

```php
$fredi->reset(); // This will reset all the settings: link text, classes and tabs hiding.
```



Support development
-------------------------------------------------

I built Fredi for my own needs and I really hope it will be helpful tool for you too.
I have no plans to make it commercial. If you find Fredi valuable for you and want to
support future development, please consider [donating few bucks] [donation]. Thanks!

[donation]: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9NQMHQE84QF22

If you are short on money, you can also support yourself by listening great Finnish singer
Fredi.



License
-------------------------------------------------

Fredi is licensed under GNU/GPL V2

Uses few images from tinybox2 modal, which is free of charge for both personal or
commercial projects under the creative commons license.
SOURCE: http://www.scriptiny.com/2011/03/javascript-modal-windows/