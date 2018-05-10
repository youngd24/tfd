DIGISHIP XML/EDI DOCUMENT 214 Version 0.2
-----------------------------------------------------------------

January 18, 2001


CONTENTS
-----------------------------------------------------------------
- ABOUT DIGISHIP XML/EDI 214
- DTD
- 214 STRUCTURE
- EDI, COMPARISON TO
- VALID EXAMPLES
- INVALID EXAMPLE


ABOUT
-----------------------------------------------------------------
This is a readme document for DigiShip's XML/EDI 214 document. This document has been created for shipment status communications between DigiShip and its preferred carrier network using a third-party VAN, Kleischmidt.


DTD
-----------------------------------------------------------------
DigiShip's XML/EDI 214 document uses xmledi.dtd to define its document structure. Please refer to the dtd's documentation for more information


214 STRUCTURE
-----------------------------------------------------------------
The 214 document must contain the following structure:

	<!DOCTYPE xmledi SYSTEM "xmledi.dtd">	#REQUIRED - used to point to the dtd
	<xmledi document="214">					#REQUIRED - defines document as type 214
	<transmission>							#REQUIRED - marks beginning of transmission

		<sender>RDWY</sender>					#REQUIRED - defines sender
		<receiver>DIGSHI</receiver>				#REQUIRED - defines receiver
		<date>2001-12-12</date>					#REQUIRED - YYYY-MM-DD - transmission date
		<time>15:45:05</time>					#REQUIRED - HH:MM:SS - transmission time
		
	</transmission>			#REQUIRED - ends transmission info

	<shipment>				#REQUIRED - begins shipment info

		<header>			#REQUIRED - begins shipment header. Only one <header> set allowed per <shipment>
		
			<pronumber>21412414</pronumber>		#REQUIRED - carrier pro number
			<bolnumber>213902139</bolnumber>	#REQUIRED - digiship's bolnumber
			<ponumber>213-ASD-12</ponumber>		#OPTIONAL - shipper's po number
		
		</header>			#REQUIRED - ends shipment header
		
		<status>			#REQUIRED - begins new shipment status (unlimited <status> sets may appear)
			
			<code>Delivered</code>				#REQUIRED - status message translated from carrier status codes
			<reason>Consignee At Fault</reason>	#REQUIRED - explanation of <code> message translated from carrier codes
			<city>Ithaca</city>					#REQUIRED - current city location of asset
			<state>NY</state>					#REQUIRED - current state location of asset
			<equipment>TL</equipment>			#OPTIONAL - equipment type of asset
			<equipmentnum>213123</equipmentnum> #OPTIONAL - equipment number of asset
			<date>2001-10-12</date>				#REQUIRED - YYYY-MM-DD - date of status
			<time>13:12:31</time>				#REQUIRED - HH:MM:SS - time of status
			<timezone>EST</timezone>			#REQUIRED - time zone of <time>
			<notes></notes>						#OPTIONAL - any other notes (unlimited <notes> are allowed)
				
		</status>			#REQUIRED - ends status
		
		.					#OPTIONAL - multiple <status> sets can be included
		.
		.
		
	</shipment>				#REQUIRED - ends shipment set
	
</xmledi>					#REQUIRED - ends xmledi document


EDI, COMPARISON TO
-----------------------------------------------------------------
This document was designed using the edi 214 as a guide. The following elements are associated with the EDI 214 segment listed on the right

			XML/EDI 214 ELEMENT				|	EDI 214 SEGMENT
			-----------------------------------------------------------
			<sender></sender>				|	ISA06
			<receiver></receiver>			|	ISA08
			<date></date>					|	ISA09	
			<time></time>					|	ISA10
			<pronumber></pronumber>			|	B1001
			<bolnumber></bolnumber>			|	B1002
			<ponumber></ponumber>			|	PRF01
			<code></code>					|	AT701
			<reason></reason>				|	AT702
			<city></city>					|	MS101
			<state></state>					|	MS102
			<equipment></equipment>			|	MS202
			<equipmentnum></equipmentnum>	|	MS203
			<date></date>					|	AT705
			<time></time>					|	AT706
			<timezone></timezone>			|	AT707
			<notes></notes>					|	UNKNOWN
			

VALID EXAMPLE
-----------------------------------------------------------------

<?xml version = "1.0"?>

<!DOCTYPE xmledi SYSTEM "xmledi.dtd">

<xmledi document="214">
	
	<transmission>

		<sender>BRIL</sender>
		<receiver>DIGSHI</receiver>
		<date>2001-01-15</date>
		<time>05:14:32</time>

	</transmission>

	<shipment>

		<header>
		
			<pronumber>65456421</pronumber>
			<bolnumber>3583483</bolnumber>
			<ponumber>FA-2131</ponumber>
		
		</header>
		
		<status>
			
			<code>In transit</code>
			<reason>Normal Status</reason>	
			<city>Chicago</city>
			<state>IL</state>
			<equipment>TL</equipment>
			<equipmentnum>100054</equipmentnum>
			<date>2001-01-15</date>
			<time>04:12:31</time>
			<timezone>EST</timezone>
			<notes></notes>
				
		</status>
		
		<status>
			
			<code>Loaded onto Trailer</code>
			<reason>Arrived at Shipper's Location</reason>	
			<city>Chicago</city>
			<state>IL</state>
			<equipment>TL</equipment>
			<equipmentnum>100054</equipmentnum>
			<date>2001-01-15</date>
			<time>02:12:31</time>
			<timezone>EST</timezone>
			<notes></notes>
				
		</status>
		
		<status>
			
			<code>Shipment booked</code>
			<reason>Received 204</reason>	
			<city>Chicago</city>
			<state>IL</state>
			<date>2001-01-14</date>
			<time>22:12:31</time>
			<timezone>EST</timezone>
			<notes>I know, I know, this status makes no sense</notes>
				
		</status>
		
	</shipment>
	
</xmledi>


INVALID EXAMPLE
-----------------------------------------------------------------

<?xml version = "1.0"?>

<!DOCTYPE xmledi SYSTEM "xmledie.dtd">				#DOES NOT POINT TO EXISTING DTD

<xmledi document="21232">							#INVALID DOCUMENT TYPE
	
	<transmission>									#TRANSMISSION MUST CONTAIN SENDER AND DATE

		<receiver>DIGSHI</receiver>
		<time>05:14:32</time>

	<shipment>										#APPEARS BEFORE </transmission>

		<header>									#PRONUMBER NOT PRESENT
		
			<bolnumber>3583483</bolnumber>			
			<bolnumber>3583483</bolnumber>			#ONLY ONE BOL NUMBER ALLOWED
			<ponumber>FA-2131</ponumber>
			
		
		</header>
		
		<status>
			
			<code>In transit</code>
			<reason>Normal Status</reason>	
			<city>Chicago</city>
			<state>IL</state>
			<date>2001-01-15</date>
			<time>04:12:31</time>
			<timezone>EST</timezone>
			<notes></notes>

			<code>Loaded onto Trailer</code>				#THIS IS INVALID
			<reason>Arrived at Shipper's Location</reason>	#BECAUSE THIS <STATUS>
			<city>Chicago</city>							#CONTAINS 2 SETS
			<state>IL</state>								#OF STATUSES
			<equipment>TL</equipment>						#
			<equipmentnum>100054</equipmentnum>				#MULTIPLE STATUSES
			<date>2001-01-15</date>							#MUST BE ENCLOSED BY
			<time>02:12:31</time>							#MULTIPLE <STATUS>
			<timezone>EST</timezone>						#
			<notes></notes>									#SEE EXAMPLE ABOVE

		
		</status>
		
	</shipment>
	
	</transmission>											#MUST APPEAR BEFORE <shipment>
	
</xmledi>	