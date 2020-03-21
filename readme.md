Laravel BBcode Parser and CKEditor 4 BBcode PLugin
=================================================

You shall install two things :

- CKeditor in your laravel project : then copy bbcode folder in CKeditor plugin folder
- https://github.com/PheRum/laravel-bbcode in your laravel project : ```composer require pherum/laravel-bbcode```

then you must copy and use the overide class BBCodeParser.php
- copy ```BBCodeParser.php``` in ```app/Custom/```
- import the class in your controller ```use App\Custom\BBCodeParser;```
- use it to filter your BBCode to HTML ```(new BBCodeParser)->parse($myvar)```

If you wan't to add something you propose au merge i'll check it.

I added few things :
- mailto support
- img attributes are working
- smileys are showing on type (there is a predefined list)

I use this CKEDITOR config

    $(document).ready(function() {
        var options = {
            // Add plugins providing functionality popular in BBCode environment.
            extraPlugins: 'bbcode,smiley,font,colorbutton,justify',
            // Remove unused plugins.
            removePlugins: 'bidi,dialogadvtab,div,flash,format,forms,horizontalrule,iframe,liststyle,pagebreak,showborders,stylescombo,table,tableselection,tabletools,templates',
            fontSize_sizes: "9/xx-small;10/x-small;13/small;16/medium;18/large;24/x-large;32/xx-large;",
            toolbar: [
                [ 'Source', '-', 'Save', 'NewPage', '-', 'Undo', 'Redo' ],
                [ 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat' ],
                [ 'Link', 'Unlink', 'Image', 'Smiley', 'SpecialChar' ],
                '/',
                [ 'Bold', 'Italic', 'Underline' ],
                [ 'FontSize' ],
                [ 'TextColor' ],
                [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight'],
                [ 'NumberedList', 'BulletedList', '-', 'Blockquote' ],
                [ 'Maximize' ]
            ],
            // Strip CKEditor smileys to those commonly used in BBCode.
            smiley_images: [
                'regular_smile.png', 'sad_smile.png', 'wink_smile.png', 'teeth_smile.png', 'tongue_smile.png',
                'embarrassed_smile.png', 'omg_smile.png', 'whatchutalkingabout_smile.png', 'angel_smile.png',
                'shades_smile.png', 'cry_smile.png', 'kiss.png', 'devil_smile.png', 'angry_smile.png'
            ],
            smiley_descriptions: [
                'smiley', 'sad', 'wink', 'laugh', 'cheeky', 'blush', 'surprise',
                'indecision', 'angel', 'cool', 'crying', 'kiss', 'devil', 'angry'
            ]
        };
        CKEDITOR.replace(document.getElementById('postBody'), options);
    });
