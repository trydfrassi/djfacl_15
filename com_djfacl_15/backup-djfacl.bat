set base=C:\wamp\www\joomladev
set basebak=c:\backup-joomla\djfacl-bak
set nome=djfacl
set nomecom=com_djfacl

xcopy /Y /E %base%\administrator\components\com_djfacl\*.* %basebak%\administrator\components\%nomecom%\
xcopy /Y /E %base%\components\%nomecom%\*.* %basebak%\components\%nomecom%\
xcopy /Y /E %base%\modules\mod_djfacl\*.* %basebak%\modules\mod_%nome%\
xcopy /Y /E %base%\administrator\modules\mod_djfacl_quickicon\*.* %basebak%\administrato\modules\mod_%nome%_quickicon\
xcopy /Y /E %base%\plugins\system\%nome%.php %basebak%\plugins\system\%nome%.php
xcopy /Y /E %base%\plugins\system\%nome%.xml %basebak%\plugins\system\%nome%.xml
xcopy /Y /E %base%\plugins\system\djfintegrity.php %basebak%\plugins\system\djfintegrity.php
xcopy /Y /E %base%\plugins\system\djfintegrity.xml %basebak%\plugins\system\djfintegrity.xml
xcopy /Y /E %base%\plugins\system\djflibraries\*.* %basebak%\plugins\system\djflibraries\
xcopy /Y /E %base%\plugins\system\djflibraries.xml %basebak%\plugins\system\djflibraries.xml
xcopy /Y /E %base%\plugins\content\djfcontent.php %basebak%\plugins\content\djfcontent.php
xcopy /Y /E %base%\plugins\content\djfcontent.xml %basebak%\plugins\content\djfcontent.xml
xcopy /Y /E %base%\plugins\content\djfcontent.xml %basebak%\plugins\content\djfcontent.xml
xcopy /Y /E %base%\administrator\language\it-IT\it-IT.%nomecom%.ini %basebak%\administrator\language\it-IT\it-IT.%nomecom%.ini
xcopy /Y /E %base%\administrator\language\en-GB\en-GB.%nomecom%.ini %basebak%\administrator\language\en-GB\en-GB.%nomecom%.ini
xcopy /Y /E %base%\administrator\language\it-IT\it-IT.%nomecom%.menu.ini %basebak%\administrator\language\it-IT\it-IT.%nomecom%.menu.ini
xcopy /Y /E %base%\administrator\language\en-GB\en-GB.%nomecom%.menu.ini %basebak%\administrator\language\en-GB\en-GB.%nomecom%.menu.ini
xcopy /Y /E %base%\language\it-IT\it-IT.%nomecom%.ini %basebak%\language\it-IT\it-IT.%nomecom%.ini
xcopy /Y /E %base%\language\en-GB\en-GB.%nomecom%.ini %basebak%\language\en-GB\en-GB.%nomecom%.ini


xcopy /Y /E %base%\tmp\%nomecom%\*.* %basebak%\tmp\%nomecom%\

xcopy /Y /E %base%\administrator\components\%nomecom%\assets\*.* %base%\tmp\%nomecom%\admin\assets\
xcopy /Y /E %base%\administrator\components\%nomecom%\controllers\*.* %base%\tmp\%nomecom%\admin\controllers\
xcopy /Y /E %base%\administrator\components\%nomecom%\helpers\*.* %base%\tmp\%nomecom%\admin\helpers\
xcopy /Y /E %base%\administrator\components\%nomecom%\images\*.* %base%\tmp\%nomecom%\admin\images\
xcopy /Y /E %base%\administrator\components\%nomecom%\libraries\*.* %base%\tmp\%nomecom%\admin\libraries\
xcopy /Y /E %base%\administrator\components\%nomecom%\models\*.* %base%\tmp\%nomecom%\admin\models\
xcopy /Y /E %base%\administrator\components\%nomecom%\tables\*.* %base%\tmp\%nomecom%\admin\tables\
xcopy /Y /E %base%\administrator\components\%nomecom%\views\*.* %base%\tmp\%nomecom%\admin\views\
xcopy /Y /E %base%\administrator\components\%nomecom%\install.%nome%.sql %base%\tmp\%nomecom%\admin\sql\install.%nome%.sql
xcopy /Y /E %base%\administrator\components\%nomecom%\uninstall.%nome%.sql %base%\tmp\%nomecom%\admin\sql\uninstall.%nome%.sql
xcopy /Y /E %base%\administrator\components\%nomecom%\admin.%nome%.php %base%\tmp\%nomecom%\admin\admin.%nome%.php
xcopy /Y /E %base%\administrator\components\%nomecom%\config.xml %base%\tmp\%nomecom%\admin\config.xml
xcopy /Y /E %base%\administrator\components\%nomecom%\index.html %base%\tmp\%nomecom%\admin\index.html
xcopy /Y /E %base%\administrator\components\%nomecom%\%nomecom%.xml %base%\tmp\%nomecom%\%nomecom%.xml
xcopy /Y /E %base%\administrator\components\%nomecom%\install.%nome%.php %base%\tmp\%nomecom%\install.%nome%.php
xcopy /Y /E %base%\administrator\components\%nomecom%\uninstall.%nome%.php %base%\tmp\%nomecom%\uninstall.%nome%.php


xcopy /Y /E %base%\components\%nomecom%\*.* %base%\tmp\%nomecom%\component\

xcopy /Y /E %base%\modules\mod_%nome%\*.* %base%\tmp\%nomecom%\mod_%nome%\
xcopy /Y /E %base%\administrator\modules\mod_%nome%_quickicon\*.* %base%\tmp\%nomecom%\mod_%nome%_quickicon\

xcopy /Y /E %base%\plugins\system\%nome%.php %base%\tmp\%nomecom%\plg_%nome%\%nome%.php
xcopy /Y /E %base%\plugins\system\%nome%.xml %base%\tmp\%nomecom%\plg_%nome%\%nome%.xml

xcopy /Y /E %base%\plugins\system\djfintegrity.php %base%\tmp\%nomecom%\plg_djfintegrity\djfintegrity.php
xcopy /Y /E %base%\plugins\system\djfintegrity.xml %base%\tmp\%nomecom%\plg_djfintegrity\djfintegrity.xml


xcopy /Y /E %base%\plugins\system\djflibraries\*.* %base%\tmp\%nomecom%\djflibraries\djflibraries\
xcopy /Y /E %base%\plugins\system\djflibraries.xml %base%\tmp\%nomecom%\djflibraries\djflibraries.xml



xcopy /Y /E %base%\plugins\content\djfcontent.php %base%\tmp\%nomecom%\plg_djfcontent\djfcontent.php
xcopy /Y /E %base%\plugins\content\djfcontent.xml %base%\tmp\%nomecom%\plg_djfcontent\djfcontent.xml

xcopy /Y /E %base%\administrator\language\it-IT\it-IT.%nomecom%.ini %base%\tmp\%nomecom%\admin\lang\it-IT.%nomecom%.ini
xcopy /Y /E %base%\administrator\language\en-GB\en-GB.%nomecom%.ini %base%\tmp\%nomecom%\admin\lang\en-GB.%nomecom%.ini
xcopy /Y /E %base%\administrator\language\it-IT\it-IT.%nomecom%.menu.ini %base%\tmp\%nomecom%\admin\lang\it-IT.%nomecom%.menu.ini
xcopy /Y /E %base%\administrator\language\en-GB\en-GB.%nomecom%.menu.ini %base%\tmp\%nomecom%\admin\lang\en-GB.%nomecom%.menu.ini
xcopy /Y /E %base%\language\it-IT\it-IT.%nomecom%.ini %base%\tmp\%nomecom%\component\lang\it-IT.%nomecom%.ini
xcopy /Y /E %base%\language\en-GB\en-GB.%nomecom%.ini %base%\tmp\%nomecom%\component\lang\en-GB.%nomecom%.ini




