Attribute VB_Name = "modShipment"
Option Explicit
Private Declare Function Czar_InitializeCzar Lib "C:\digiship\rating\pricing\czar32.dll" (ByVal prog_path As Any) As Integer
Private Declare Function Czar_Set_Czardata Lib "C:\digiship\rating\pricing\czar32.dll" (ByVal data_path As String) As Integer
Private Declare Sub Czar_RateShipment Lib "C:\digiship\rating\pricing\czar32.dll" (ByRef dll_in As CzarParms)
Private Declare Function Czar_disp_ver Lib "C:\digiship\rating\pricing\czar32.dll" (ByRef ver_in As VERSIONS_REC) As Integer
Private Declare Function Czar_EndCzar Lib "C:\digiship\rating\pricing\czar32.dll" () As Integer

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
    mc_ID(MCMAX - 1) As String * 1 ' 0= SS, 1= Multi
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




Public Sub MakeApiCalls(ByVal s_ProgPath As String, ByVal s_DataPath As String, ByVal strWeight As String, ByVal strClass As String, ByVal strOrgZip As String, ByVal strDestZip As String)
    '*** Declarations ***
    Dim n_ErrorNumber As Integer
    Dim Temp As String
    Dim t_class As String
    
    n_ErrorNumber = Czar_InitializeCzar(s_ProgPath)
    n_ErrorNumber = Czar_Set_Czardata(s_DataPath)
    Parms.Header.s_OrgZip = strOrgZip
    Parms.Header.s_DestZip = strDestZip
    
    Parms.Detail1.s_Class = Right(strClass, 4)
    Temp = strWeight
    t_class = Parms.Detail1.s_Class
    Temp = "000000" & Temp
    Parms.Detail1.s_Weight = Right(Temp, 6)
    Call Czar_RateShipment(Parms)
    Call Czar_EndCzar
    
    
End Sub
