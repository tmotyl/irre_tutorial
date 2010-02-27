=====================================================================================================
TYPO3 4.1: Inline Relational Record Editing Tutorial
(c) 2007 Oliver Hader <oh@inpublica.de> - All rights reserved
=====================================================================================================
This script is part of the TYPO3 project. The TYPO3 project is free software; you can redistribute
it and/or modify it under the terms of the GNU General Public License as published by the Free So
Software Foundation; either version 2 of the License, or (at your option) any later version.
The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
=====================================================================================================


-----------------------------
 1. Installation
---------------------------
Download the extension "irre_tutorial" from the TYPO3 Extension Repository at http://www.typo3.org/
Install the extension using the Extension Manager (EM) in the TYPO3 Backend. You need administrative
rights (admin user) at your TYPO3 installation to be able to do this.

After this extension was installed, a new backend module "Web>IRRE Tutorial" will appear. By clicking
on the module link in the left menu frame, you can import a set of sample data.

To uninstall the sample data you have to select "Uninstall sample data" from the upper right corner
in the "IRRE Tutorial" module. The script looks for a page alias "irre_tutorial_data" and removes, if
successfully found, this branch from your TYPO3 installation.



-----------------------------
 2. Usage
---------------------------
After you imported the IRRE Tutorial sample data, you are can play around, change tca.php files etc.
You can just do, what ever you'd like to. The ext_tables.php and tca.php have a additional string in
their filenames. This should help you to find the case you're looking fore faster. See the following
description what these strings (e.g. like "tx_irretutorial_<string>_hotel or tca.<string>.php) mean:

	* 1ncsv: 1:n relations using comma separated values as list
	* 1nff: 1:n relations using foreign_field as pointer on child table
	* mnasym: m:n bidirectional asymmetric relations using intermediate table
	* mnsym: m:n bidirectional symmetric relations using intermediate table
	* mnattr: m:n bidirectional asymmetric attributed relations using intermediate table


	
-----------------------------
 3. To-Do
---------------------------
There will be a more detailed documentation which is currently prepared as diploma thesis depending
on "Inline Relational Record Editing". When this "big documentation" is finished, it will be attached
to this extension.

The release of the diploma thesis by Oliver Hader is scheduled to 28th of February 2007.
The release of TYPO3 4.1-final by the TYPO3 Core Team and the TYPO3 Association is currently scheduled
to 19th of February.



-----------------------------
 4. Links
---------------------------
Wiki:		http://wiki.typo3.org/index.php/IRRE
Core API:	http://typo3.org/documentation/document-library/core-documentation/doc_core_api/current/
Exntension:	http://typo3.org/extensions/repository/view/irre_tutorial/
inpublica:	http://www.inpublica.de/


=====================================================================================================