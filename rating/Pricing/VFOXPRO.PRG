******************************************************************
*The Starting Part of the Record ***************************
StructID="F"

******************************************************************
*Header Layout Assignments***************************
Rcdcode         ="01"
ErrorStatCd     =space(4)
InfoFlag        =" "
MultSingMd      ="M"
ErrorFileMd     ="E"
CompPartMd      ="1"
TrfName         ="TESTCZ02"
IntrastSw       ="N"
ShipDt          ="10-01-1995"
OrigZipCd       ="38104 "
OrigSt          ="TN"
OrigCity        ="Memphis          "
DestZipCd       ="37211 "
DestSt          ="TN"
DestCity        ="Nashville        "
NumDetlRcds     ="01"
TrfAuth         ="TarfAuth"
RateBasisNo     ="-RateBasis-"
ActualWt        ="ActlWt"
BilledWt        ="BildWt"
MinChg          ="MinChrg"
DeficitRate     ="DeficRt"
DeficitWt       ="DefiWt"
DeficitChg      ="DeficitCh"
TotalChg        ="TotalChrg"
TrfEffectDt     ="10-15-1997"
IDNo            ="SixteenCharIDNumb"
SingleShip      ="N"
TLMin           ="TLMinimm"
TLMax           ="TLMaximm"
UseDiscs        ="N"
Filler1         =" "
OrigRtngFlag    ="D"
DestRtngFlag    ="D"
ComparativeChg  ="ComparChg"
VarianceAmt     ="VarAmt"
RtngFlagOver    ="D"
DiscAppl        ="C"
SingShipAdditive="SglSh"
LHDiscAmt       ="LHDiscAmt"
MCDiscAmt       ="MCDsAmt"
RateAdjustFactor="RtAdj"
Reserved22      ="Reserved-----Reserved"
EndCap          =chr(13)+chr(10)

* Build the HeaderLayout String**************

HeaderLayout=Rcdcode+ ErrorStatCd+ InfoFlag+ MultSingMd+ ErrorFileMd+;
CompPartMd+ TrfName+ IntrastSw+ ShipDt+ OrigZipCd+ OrigSt+;
OrigCity+ DestZipCd+ DestSt+ DestCity+ NumDetlRcds+ TrfAuth+;
RateBasisNo+ ActualWt+ BilledWt+ MinChg+ DeficitRate+ DeficitWt+;
DeficitChg+ TotalChg+ TrfEffectDt+ IDNo+ SingleShip+ TLMin+ TLMax+;
UseDiscs+ Filler1+ OrigRtngFlag+ DestRtngFlag+ ComparativeChg+;
VarianceAmt+ RtngFlagOver+ DiscAppl+ SingShipAdditive+ LHDiscAmt+;
MCDiscAmt+ RateAdjustFactor+ Reserved22+ EndCap

******************************************************************
* Assign the DetailLayout Variables*********************
DTR01="0260     20RetrnWtReturnChg"+EndCap+" "
DTR02="0277 2222224444444555555555"+EndCap+" "
DTR03="0277 2222224444444555555555"+EndCap+" "
DTR04="0277 2222224444444555555555"+EndCap+" "
DTR05="0277 2222224444444555555555"+EndCap+" "
DTR06="0277 2222224444444555555555"+EndCap+" "
DTR07="0277 2222224444444555555555"+EndCap+" "
DTR08="0277 2222224444444555555555"+EndCap+" "
DTR09="0277 2222224444444555555555"+EndCap+" "
DTR10="0277 2222224444444555555555"+EndCap+" "
DTR11="0277 2222224444444555555555"+EndCap+" "
DTR12="0277 2222224444444555555555"+EndCap+" "
DTR13="0277 2222224444444555555555"+EndCap+" "
DTR14="0277 2222224444444555555555"+EndCap+" "
DTR15="0277 2222224444444555555555"+EndCap+" "
DTR16="0277 2222224444444555555555"+EndCap+" "
DTR17="0277 2222224444444555555555"+EndCap+" "
DTR18="0277 2222224444444555555555"+EndCap+" "
DTR19="0277 2222224444444555555555"+EndCap+" "
DTR20="0277 2222224444444555555555"+EndCap+" "
* Build the DetailLayout String*********************
DetailLayout=DTR01+ DTR02+ DTR03+ DTR04+ DTR05+ ;
             DTR06+ DTR07+ DTR08+ DTR09+ DTR10+ ;
             DTR11+ DTR12+ DTR13+ DTR14+ DTR15+ ;
             DTR16+ DTR17+ DTR18+ DTR19+ DTR20

******************************************************************
* Assign the DiscountLayoutD% Variables*********************
RecCdD     ="D%"
MinChgDiscD=" 1234"
L5CDiscD   =" 1234"
M5CDiscD   =" 1234"
M1MDiscD   =" 1234"
M2MDiscD   =" 1234"
M5MDiscD   =" 1234"
M10MDiscD  =" 1234"
M20MDiscD  =" 1234"
M30MDiscD  =" 1234"
M40MDiscD  =" 1234"

* Build the DiscountLayoutD% String*********************
DiscountLayoutD=MinChgDiscD + L5CDiscD + M5CDiscD + M1MDiscD +;
M2MDiscD + M5MDiscD + M10MDiscD + M20MDiscD + M30MDiscD + M40MDiscD+ EndCap

******************************************************************
* Assign the DiscountLayoutI% Variables*********************
RecCdI     ="I%"
MinChgDiscI=" 1234"
L5CDiscI   =" 1234"
M5CDiscI   =" 1234"
M1MDiscI   =" 1234"
M2MDiscI   =" 1234"
M5MDiscI   =" 1234"
M10MDiscI  =" 1234"
M20MDiscI  =" 1234"
M30MDiscI  =" 1234"
M40MDiscI  =" 1234"

* Build the DiscountLayoutI% String*********************
DiscountLayoutI=MinChgDiscI+ L5CDiscI + M5CDiscI + M1MDiscI +;
M2MDiscI + M5MDiscI + M10MDiscI + M20MDiscI + M30MDiscI + M40MDiscI+ EndCap

* Create a filler Variable**********************
Filler3="   "

******************************************************************
* Assign the ExpandedRateLayout Variables*********************
MinChgs=replicate("4321",4)
Rates  =replicate("9876",171)

* Build the ExpandedRateLayout String*********************
ExpandedRateLayout=MinChgs+Rates


******************************************************************
* Assign the ClassWeightTableLayout Variables*********************
ClassTbl  =replicate("123456",20)
WtTbl     =replicate("4321",20)
ClassCount="  "
WtCount   ="  "

* Build the ClassWeightTableLayout String*********************
ClassWeightTableLayout=ClassTbl + WtTbl + ClassCount + WtCount


******************************************************************
* Assign the MinimumChargeLayout Variables*********************
MCID     =replicate("01",15)
Filler2  ="  "
MCLowWts =replicate("6543",30)
MinChgs  =replicate("8765",30)
MCType   ="  "
MCCount  ="  "

* Build the MinimumChargeLayout String*********************
MinimumChargeLayout=MCID + Filler2 + MCLowWts + MinChgs + MCType + MCCount



* Use the filler Variable**********************
Filler3  ="   "

* Build odd Variables needed*********************
EndDllVar="*"

******************************************************************
* Assemble the Final Record *********************

public RateRecord

RateRecord=StructID+ HeaderLayout+ DetailLayout+ DiscountLayoutD+;
DiscountLayoutI+ Filler3 + ExpandedRateLayout+ ClassWeightTableLayout+;
MinimumChargeLayout + Filler3 + EndDllVar

* Call the Initializing function *********************
declare Czar_InitializeCzar in c:\czardev\czar32.dll as cInit string
?cInit("c:\czardev\")      && Initialize the Library

* Set the data directory *********************
declare Czar_Set_Czardata in c:\czardev\czar32.dll as czsetdata string
datapath="c:\czardev\"
?czsetdata(@datapath)     && Set the data directory up ('@' for pointer to a string)

** Show dll version ***********
*!*   declare Czar_disp_ver in c:\czardev\czar32.dll as czver string
*!*   xom=space(105)
*!*   ?czver(@xom)
*!*   if !used("zoom")
*!*     use zoom
*!*   endif
*!*   select zoom
*!*   replace bigun with xom
*!*   browse

** Rate the record we constructed ***********
declare Czar_RateShipment in c:\czardev\czar32.dll as czrate string
?czrate(@raterecord)        && ampersand because it's a pointer to a string

** Declare and call the closing function ***********
declare Czar_EndCzar in c:\czardev\czar32.dll as cEnd
?cEnd()                 && Close up shop

** show results in a messagewindow to see error code
=messagebox(substr(raterecord,2,50))

** Use the results of all this and look at them
* create table zoom (bigun m(4))   && create a table with one memo field to see rec
if !used("zoom")
  use zoom                         && open the table if it isn't open
endif

select zoom                        && go to that table
replace bigun with raterecord      && put the structure in the memo field
browse                             && browse the table so we can see the memo field
