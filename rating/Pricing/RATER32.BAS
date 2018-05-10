Attribute VB_Name = "Module1"
Option Explicit

Type HeaderRecord
    s_RecordCode As String * 2
    s_ErrorStatus As String * 4
    s_ProgramMode As String * 4
    s_TariffName As String * 8
    s_IntraState As String * 1
    s_ShipmentDate As String * 10
    s_OrgZip As String * 6
    s_OrgCityState As String * 19
    s_DestZip As String * 6
    s_DestCityState As String * 19
    s_DetailLines As String * 2
    s_TariffAuthority As String * 8
    s_Rbno As String * 11
    s_ActualWeight As String * 6
    s_BilledWeight As String * 6
    s_MinCharge As String * 7
    s_DFCRate As String * 7
    s_DFCWeight As String * 6
    s_DFCCharges As String * 9
    s_TotalCharges As String * 9
    s_EffectiveDate As String * 10
    s_ProNumber As String * 16
    s_SingleShipment As String * 1
    s_TLMin As String * 8
    s_TLMax As String * 8
    s_use_dscnts As String * 1
    s_return_pointnms As String * 1
    s_org_rout As String * 1
    s_dst_rout As String * 1
    s_CarrierCharges As String * 9
    s_Variance As String * 6
    s_RoutingFlag As String * 1
    s_DiscountApplic As String * 1
    s_SSAdditive As String * 5
    s_LHDiscountAmt As String * 9
    s_MCDiscountAmt As String * 7
    s_RateAdjFactor As String * 5
    s_UserFiller As String * 22
    s_CRLF As String * 2
End Type

Type DetailRecord
    s_RecordCode As String * 2
    s_Class As String * 3
    s_Weight As String * 6
    s_Rate As String * 7
    s_Charge As String * 9
    s_CRLF As String * 2
    s_filler As String * 1
End Type

Public Const MCMAX As Integer = 30

Type MINCHG_REC
    mc_ID(MCMAX-1) As String * 1   ' 0= SS, 1= Multi
    mc_fill1 As String * 2
    mc_low_wgt(MCMAX - 1) As Long
    mc_chg(MCMAX - 1) As Long
    mc_type As Integer
    mc_cnt As Integer
End Type

Type DiscountField
    s_filler As String * 1
    s_Percent As String * 4
End Type

Type DiscountRecord
    s_RecordCode As String * 2
    ut_Discount(9) As DiscountField
    s_CRLF As String * 2
End Type

Type WBDISC_IN_REC
    record_code As String * 2   '  2 D%
    dscnt(9) As String * 5      ' 52 (_99v99) _ = space
    crlf As String * 2          '
End Type

Type VERSIONS_REC
     czar_prog_nm  As String * 20
    czar_ver_number  As String * 3
    czar_mod_number  As String * 5
    bczar_prog_nm  As String * 20
    bczar_ver_number  As String * 3
    bczar_mod_number  As String * 5
    fxlib_prog_nm  As String * 20
    fxlib_ver_number  As String * 3
    fxlib_mod_number  As String * 5
    datatype  As String * 15
    compiler  As String * 6
End Type

Public Const DETAIL_MAX As Integer = 20
Public Const EXPAND_CNT As Integer = 175
Public ver_in As VERSIONS_REC


Type CzarParms
    Test As String * 1
    Header As HeaderRecord
    Detail1  As DetailRecord
    Detail2  As DetailRecord
    Detail3  As DetailRecord
    Detail4  As DetailRecord
    Detail5  As DetailRecord
    Detail6  As DetailRecord
    Detail7  As DetailRecord
    Detail8  As DetailRecord
    Detail9  As DetailRecord
    Detail10  As DetailRecord
    Detail11 As DetailRecord
    Detail12 As DetailRecord
    Detail13 As DetailRecord
    Detail14 As DetailRecord
    Detail15 As DetailRecord
    Detail16 As DetailRecord
    Detail17 As DetailRecord
    Detail18 As DetailRecord
    Detail19 As DetailRecord
    Detail20 As DetailRecord
    wbdisc_in As WBDISC_IN_REC
    indir_in As WBDISC_IN_REC
    Fill1 As String * 3
    expanded_rates(EXPAND_CNT - 1) As Long
    class_tab(19) As String * 6
    wgt_tab(19) As Long
    class_cnt As Integer
    wgt_cnt As Integer
    minchgs As MINCHG_REC
    Fill2 As String * 3
    EndDLL As String * 1
End Type

Public Parms As CzarParms

Public Temp As String * 100
Public n_Result As Boolean

Declare Function Czar_InitializeCzar Lib "C:\CZARDEV\czar32.dll" (ByVal prog_path As Any) As Integer
Declare Function Czar_Set_Czardata Lib "C:\CZARDEV\czar32.dll" (ByVal data_path As String) As Integer
Declare Sub Czar_RateShipment Lib "C:\CZARDEV\czar32.dll" (ByRef dll_in As CzarParms)
Declare Function Czar_disp_ver Lib "C:\CZARDEV\czar32.dll" (ByRef ver_in As VERSIONS_REC) As Integer
Declare Function Czar_EndCzar Lib "C:\CZARDEV\czar32.dll" () As Integer

Public Sub Main()
'***********************************************************************************/
'* This subroutine calls the necessary functions to rate a bill.                   */
'***********************************************************************************/

'*** Directives ***

    'On Error GoTo RateError

'*** Declarations ***

    Dim s_ProgPath As String * 177
    Dim s_DataPath As String * 177
    Dim n_ErrorNumber As Integer

'*** Code ***
    ClearHeader

    s_ProgPath = ""
    s_ProgPath = "C:\CZARDEV\" + Chr(0)  ' Must be NULL terminated
    s_DataPath = ""
    s_DataPath = "C:\CZARDEV\" + Chr(0)  ' Must be NULL terminated
    n_ErrorNumber = 0

    Parms.Header.s_RecordCode = "01"
    Parms.Header.s_ProgramMode = " ME1"
    Parms.Header.s_ShipmentDate = "01-01-1995"
    Parms.Header.s_TariffName = "TESTCZ02"
    Parms.Header.s_DetailLines = "01"
    Parms.Header.s_SingleShipment = "N"
    Parms.Header.s_IntraState = "N"
    Parms.Test = "F"
    Parms.EndDLL = "*"
    Parms.Detail1.s_RecordCode = "02"
'    Parms.Detail2.s_RecordCode = "02"

    DispCzar

    n_ErrorNumber = Czar_InitializeCzar(s_ProgPath)
    Debug.Print "Prog ErrorNumber:"; n_ErrorNumber
    n_ErrorNumber = Czar_Set_Czardata(s_DataPath)
    Debug.Print "Data ReturnNumber:"; n_ErrorNumber
    n_ErrorNumber = Czar_disp_ver(ver_in)
    Debug.Print "Czar version:"; ver_in.fxlib_ver_number
    Debug.Print "Czar Modification:"; ver_in.fxlib_mod_number
    Debug.Print "Czar Type:"; ver_in.datatype
    Debug.Print "Czar Compiler:"; ver_in.compiler
    Load Form1
    Form1.Show

End Sub
Public Sub ClearHeader()
Dim x As Integer
'Clear the header
     Parms.Header.s_RecordCode = Space(2)
     Parms.Header.s_ErrorStatus = Space(4)
     Parms.Header.s_ProgramMode = Space(4)
     Parms.Header.s_TariffName = Space(8)
     Parms.Header.s_IntraState = Space(1)
     Parms.Header.s_ShipmentDate = Space(10)
     Parms.Header.s_OrgZip = Space(6)
     Parms.Header.s_OrgCityState = Space(19)
     Parms.Header.s_DestZip = Space(6)
     Parms.Header.s_DestCityState = Space(19)
     Parms.Header.s_DetailLines = Space(2)
     Parms.Header.s_TariffAuthority = Space(8)
     Parms.Header.s_Rbno = Space(11)
     Parms.Header.s_ActualWeight = Space(6)
     Parms.Header.s_BilledWeight = Space(6)
     Parms.Header.s_MinCharge = Space(7)
     Parms.Header.s_DFCRate = Space(7)
     Parms.Header.s_DFCWeight = Space(6)
     Parms.Header.s_DFCCharges = Space(9)
     Parms.Header.s_TotalCharges = Space(9)
     Parms.Header.s_EffectiveDate = Space(10)
     Parms.Header.s_ProNumber = Space(16)
     Parms.Header.s_SingleShipment = Space(1)
     Parms.Header.s_TLMin = Space(8)
     Parms.Header.s_TLMax = Space(8)
     Parms.Header.s_use_dscnts = Space(1)
     Parms.Header.s_return_pointnms = Space(1)
     Parms.Header.s_org_rout = Space(1)
     Parms.Header.s_dst_rout = Space(1)
     Parms.Header.s_CarrierCharges = Space(9)
     Parms.Header.s_Variance = Space(6)
     Parms.Header.s_RoutingFlag = Space(1)
     Parms.Header.s_DiscountApplic = Space(1)
     Parms.Header.s_SSAdditive = Space(5)
     Parms.Header.s_LHDiscountAmt = Space(9)
     Parms.Header.s_MCDiscountAmt = Space(7)
     Parms.Header.s_RateAdjFactor = Space(5)
     Parms.Header.s_UserFiller = Space(22)
     Parms.Header.s_CRLF = "**" 'Space(2)

' Clear the details
     Parms.Detail1.s_Class = Space(3)
     Parms.Detail1.s_Weight = Space(6)
     Parms.Detail1.s_Rate = Space(7)
     Parms.Detail1.s_Charge = Space(9)
     Parms.Detail1.s_CRLF = Space(2)
     Parms.Detail1.s_filler = Space(1)

     Parms.Detail2.s_Class = Space(3)
     Parms.Detail2.s_Weight = Space(6)
     Parms.Detail2.s_Rate = Space(7)
     Parms.Detail2.s_Charge = Space(9)
     Parms.Detail2.s_CRLF = Space(2)
     Parms.Detail2.s_filler = Space(1)

     Parms.Detail3.s_Class = Space(3)
     Parms.Detail3.s_Weight = Space(6)
     Parms.Detail3.s_Rate = Space(7)
     Parms.Detail3.s_Charge = Space(9)
     Parms.Detail3.s_CRLF = Space(2)
     Parms.Detail3.s_filler = Space(1)

     Parms.Detail4.s_Class = Space(3)
     Parms.Detail4.s_Weight = Space(6)
     Parms.Detail4.s_Rate = Space(7)
     Parms.Detail4.s_Charge = Space(9)
     Parms.Detail4.s_CRLF = Space(2)
     Parms.Detail4.s_filler = Space(1)

     Parms.Detail5.s_Class = Space(3)
     Parms.Detail5.s_Weight = Space(6)
     Parms.Detail5.s_Rate = Space(7)
     Parms.Detail5.s_Charge = Space(9)
     Parms.Detail5.s_CRLF = Space(2)
     Parms.Detail5.s_filler = Space(1)

     Parms.Detail6.s_Class = Space(3)
     Parms.Detail6.s_Weight = Space(6)
     Parms.Detail6.s_Rate = Space(7)
     Parms.Detail6.s_Charge = Space(9)
     Parms.Detail6.s_CRLF = Space(2)
     Parms.Detail6.s_filler = Space(1)

     Parms.Detail7.s_Class = Space(3)
     Parms.Detail7.s_Weight = Space(6)
     Parms.Detail7.s_Rate = Space(7)
     Parms.Detail7.s_Charge = Space(9)
     Parms.Detail7.s_CRLF = Space(2)
     Parms.Detail7.s_filler = Space(1)

     Parms.Detail8.s_Class = Space(3)
     Parms.Detail8.s_Weight = Space(6)
     Parms.Detail8.s_Rate = Space(7)
     Parms.Detail8.s_Charge = Space(9)
     Parms.Detail8.s_CRLF = Space(2)
     Parms.Detail8.s_filler = Space(1)

     Parms.Detail9.s_Class = Space(3)
     Parms.Detail9.s_Weight = Space(6)
     Parms.Detail9.s_Rate = Space(7)
     Parms.Detail9.s_Charge = Space(9)
     Parms.Detail9.s_CRLF = Space(2)
     Parms.Detail9.s_filler = Space(1)

     Parms.Detail10.s_Class = Space(3)
     Parms.Detail10.s_Weight = Space(6)
     Parms.Detail10.s_Rate = Space(7)
     Parms.Detail10.s_Charge = Space(9)
     Parms.Detail10.s_CRLF = Space(2)
     Parms.Detail10.s_filler = Space(1)

     Parms.Detail11.s_Class = Space(3)
     Parms.Detail11.s_Weight = Space(6)
     Parms.Detail11.s_Rate = Space(7)
     Parms.Detail11.s_Charge = Space(9)
     Parms.Detail11.s_CRLF = Space(2)
     Parms.Detail11.s_filler = Space(1)

     Parms.Detail12.s_Class = Space(3)
     Parms.Detail12.s_Weight = Space(6)
     Parms.Detail12.s_Rate = Space(7)
     Parms.Detail12.s_Charge = Space(9)
     Parms.Detail12.s_CRLF = Space(2)
     Parms.Detail12.s_filler = Space(1)

     Parms.Detail13.s_Class = Space(3)
     Parms.Detail13.s_Weight = Space(6)
     Parms.Detail13.s_Rate = Space(7)
     Parms.Detail13.s_Charge = Space(9)
     Parms.Detail13.s_CRLF = Space(2)
     Parms.Detail13.s_filler = Space(1)

     Parms.Detail14.s_Class = Space(3)
     Parms.Detail14.s_Weight = Space(6)
     Parms.Detail14.s_Rate = Space(7)
     Parms.Detail14.s_Charge = Space(9)
     Parms.Detail14.s_CRLF = Space(2)
     Parms.Detail14.s_filler = Space(1)

     Parms.Detail15.s_Class = Space(3)
     Parms.Detail15.s_Weight = Space(6)
     Parms.Detail15.s_Rate = Space(7)
     Parms.Detail15.s_Charge = Space(9)
     Parms.Detail15.s_CRLF = Space(2)
     Parms.Detail15.s_filler = Space(1)

     Parms.Detail16.s_Class = Space(3)
     Parms.Detail16.s_Weight = Space(6)
     Parms.Detail16.s_Rate = Space(7)
     Parms.Detail16.s_Charge = Space(9)
     Parms.Detail16.s_CRLF = Space(2)
     Parms.Detail16.s_filler = Space(1)

     Parms.Detail17.s_Class = Space(3)
     Parms.Detail17.s_Weight = Space(6)
     Parms.Detail17.s_Rate = Space(7)
     Parms.Detail17.s_Charge = Space(9)
     Parms.Detail17.s_CRLF = Space(2)
     Parms.Detail17.s_filler = Space(1)

     Parms.Detail18.s_Class = Space(3)
     Parms.Detail18.s_Weight = Space(6)
     Parms.Detail18.s_Rate = Space(7)
     Parms.Detail18.s_Charge = Space(9)
     Parms.Detail18.s_CRLF = Space(2)
     Parms.Detail18.s_filler = Space(1)

     Parms.Detail19.s_Class = Space(3)
     Parms.Detail19.s_Weight = Space(6)
     Parms.Detail19.s_Rate = Space(7)
     Parms.Detail19.s_Charge = Space(9)
     Parms.Detail19.s_CRLF = Space(2)
     Parms.Detail19.s_filler = Space(1)

     Parms.Detail20.s_Class = Space(3)
     Parms.Detail20.s_Weight = Space(6)
     Parms.Detail20.s_Rate = Space(7)
     Parms.Detail20.s_Charge = Space(9)
     Parms.Detail20.s_CRLF = Space(2)
     Parms.Detail20.s_filler = Space(1)

     Parms.expanded_rates(0) = 999999
     Parms.class_tab(0) = "999   "
     Parms.minchgs.mc_ID = "123456789_123456789_123456789_"
     Parms.wbdisc_in.record_code = "D%"
     Parms.indir_in.record_code = "  "

End Sub



Public Sub DispCzar()
Dim x As Integer

    ' Display returned values

Debug.Print Parms.Header.s_RecordCode; "|"; Parms.Header.s_ErrorStatus; "|"; Parms.Header.s_ProgramMode; "|";
Debug.Print "|"; Parms.Header.s_TariffName; "|"; Parms.Header.s_IntraState; "|"; Parms.Header.s_ShipmentDate; "|"
Debug.Print "|"; Parms.Header.s_OrgZip; "|"; Parms.Header.s_OrgCityState; "|"; Parms.Header.s_DestZip; "|"; Parms.Header.s_DestCityState; "|"
Debug.Print "|"; Parms.Header.s_DetailLines; "|"; Parms.Header.s_TariffAuthority; "|"; Parms.Header.s_Rbno; "|"; _
                 Parms.Header.s_ActualWeight; "|"; Parms.Header.s_BilledWeight; "|"; Parms.Header.s_MinCharge; "|"
Debug.Print "|"; Parms.Header.s_DFCRate; "|"; Parms.Header.s_DFCWeight; "|"; Parms.Header.s_DFCCharges; "|"; Parms.Header.s_TotalCharges; "|"; Parms.Header.s_EffectiveDate; "|"; Parms.Header.s_ProNumber; "|"
Debug.Print "|"; Parms.Header.s_SingleShipment; "|"; Parms.Header.s_TLMin; "|"; Parms.Header.s_TLMax '; _
'                "|"; Parms.Header.s_usedscnts; "|"; Parms.Header.s_returnpointnms; "|"; Parms.Header.s_orgrout; Parms.Header.s_dstrout
'    Debug.Print Parms.HEader.s_carriercharges
'    Parms.HEader.s_variance
'    Parms.HEader.s_userfiller
'    Parms.HEader.s_Crlf


    Debug.Print "1|"; Parms.Detail1.s_RecordCode; "|"; Parms.Detail1.s_Class; "|"; Parms.Detail1.s_Weight; _
    "|"; Parms.Detail1.s_Rate; "|"; Parms.Detail1.s_Charge; "|"; Parms.Detail1.s_filler; "|"
'    Parms.Detail(x).s_Crlf
    Debug.Print "2|"; Parms.Detail2.s_RecordCode; "|"; Parms.Detail2.s_Class; "|"; Parms.Detail2.s_Weight; _
    "|"; Parms.Detail2.s_Rate; "|"; Parms.Detail2.s_Charge; "|"; Parms.Detail2.s_filler; "|"
'    Parms.Detail(x).s_Crlf
    Debug.Print "11|"; Parms.Detail11.s_RecordCode; "|"; _
    Parms.Detail11.s_Class; "|"; Parms.Detail11.s_Weight; "|"; _
    Parms.Detail11.s_Rate; "|"; Parms.Detail11.s_Charge; "|"; _
    Parms.Detail11.s_filler; "|"
'    Parms.Detail(x).s_Crlf

Debug.Print " 10 Rates:";
'    For x = 0 To 174
For x = 0 To 9
    Debug.Print Parms.expanded_rates(x); "|";
Next x
Debug.Print "Last:"; Parms.expanded_rates(165); "|";
Debug.Print

For x = 0 To 19
    Debug.Print Parms.class_tab(x); "|";
Next x
Debug.Print
For x = 0 To 19
    Debug.Print Parms.wgt_tab(x); "|";
Next x
Debug.Print
Debug.Print "classes:"; Parms.class_cnt; "wgts:"; Parms.wgt_cnt

Debug.Print "MC data:"; Parms.minchgs.mc_type; Parms.minchgs.mc_cnt
Debug.Print "  mc_ID:|"; Parms.minchgs.mc_ID; "|"
For x = 0 To 29
    Debug.Print Parms.minchgs.mc_low_wgt(x); ":"; Parms.minchgs.mc_chg(x); "|";
Next x
Debug.Print

'    Parms.bdisc_in.record_code = Space(2)
'    Parms.indir_in.record_code = Space(2)
'    For x = 0 To 9
'        Parms.bdisc_in.dscnt(x) = Space(5)
'        Parms.indir_in.dscnt(x) = Space(5)
'    Next x
'    Parms.indir_in.Crlf = Chr$(13) & Chr$(10)
'    Parms.bdisc_in.Crlf = Chr$(13) & Chr$(10)

Debug.Print "Test:"; Parms.Test
Debug.Print Len(Parms)

End Sub

