VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "RateShipment"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = True

Option Explicit
Public CVSID As String




Public Function RateShipment(ByVal strOrgZip As String, ByVal strDestZip As String, ByVal strClass As String, ByVal strWeight As String) As Currency
    '*** Declarations ***
    Dim s_ProgPath As String
    Dim s_DataPath As String
    Dim n_ErrorNumber As Integer
    Dim Temp As String
    Dim t_class As String
    Dim FileNum As Long
    Dim tempArr() As String
    Dim FieldAttribs() As String
    Dim i As Long, x As Long
    Dim s_Path As String
    Dim tempStr As String
    s_Path = "C:\digiship\rating\conf\RateConfig.txt"
           
    '*** Code ***
    ClearHeader
    s_ProgPath = ""
    s_DataPath = ""
    
    '//////////////// Read Config file ////////////////
    FileNum = FreeFile()
    Open s_Path For Input As FileNum
    tempStr = Input(LOF(FileNum), #FileNum)
    tempArr = Split(tempStr, ", ")
    Close #FileNum
    For i = 0 To UBound(tempArr)
        FieldAttribs = Split(tempArr(i), "|")
                    'FieldAttribs (0) <-- FieldName
                    'FieldAttribs (1) <-- Default Value
        Select Case UCase(FieldAttribs(0))
            
            Case "RECORDCODE"
                If UBound(FieldAttribs) = 1 Then
                    Parms.Header.s_RecordCode = FieldAttribs(1)
                Else
                    ' Populate Parms.Header.s_RecordCode through code
                End If
            Case "PROGRAMMODE"
                If UBound(FieldAttribs) = 1 Then
                    Parms.Header.s_ProgramMode = FieldAttribs(1)
                Else
                    ' Populate Parms.Header.s_ProgramMode through code
                End If
            Case "SHIPMENTDATE"
                If UBound(FieldAttribs) = 1 Then
                    Parms.Header.s_ShipmentDate = FieldAttribs(1)
                Else
                    ' Populate Parms.Header.s_ShipmentDate through code
                End If
            Case "TARIFFNAME"
                If UBound(FieldAttribs) = 1 Then
                    Parms.Header.s_TariffName = FieldAttribs(1)
                Else
                    ' Populate Parms.Header.s_TariffName through code
                End If
            Case "DETAILLINES"
                If UBound(FieldAttribs) = 1 Then
                    Parms.Header.s_DetailLines = FieldAttribs(1)
                Else
                    ' Populate Parms.Header.s_DetailLines through code
                End If
            Case "SINGLESHIPMENT"
                If UBound(FieldAttribs) = 1 Then
                    Parms.Header.s_SingleShipment = FieldAttribs(1)
                Else
                    ' Populate Parms.Header.s_SingleShipment through code
                End If
            Case "INTRASTATE"
                If UBound(FieldAttribs) = 1 Then
                    Parms.Header.s_IntraState = FieldAttribs(1)
                Else
                    ' Populate Parms.Header.s_IntraState through code
                End If
            Case "USEDISCOUNTS"
                If UBound(FieldAttribs) = 1 Then
                    Parms.Header.s_use_dscnts = FieldAttribs(1)
                Else
                    ' Populate Parms.Header.s_IntraState through code
                End If
            Case "DISCAPPL"
                If UBound(FieldAttribs) = 1 Then
                    Parms.Header.s_DiscountApplic = FieldAttribs(1)
                Else
                    ' Populate Parms.Header.s_IntraState through code
                End If
            Case "PROGRAMPATH"
                s_ProgPath = FieldAttribs(1) + Chr(0)  ' Must be NULL terminated
                s_DataPath = FieldAttribs(1) + Chr(0)  ' Must be NULL terminated
            
       
        End Select
    Next 'i
    
    n_ErrorNumber = 0
    
'   s_ProgPath = ""
'   s_ProgPath = "C:\CZARDEV\" + Chr(0)  ' Must be NULL terminated
'   s_DataPath = ""
'   s_DataPath = "C:\CZARDEV\" + Chr(0)  ' Must be NULL terminated
'   n_ErrorNumber = 0
    
    ' To be handled with a config file later
'   Parms.Header.s_RecordCode = "01"
'   Parms.Header.s_ProgramMode = " ME1"
'   Parms.Header.s_ShipmentDate = "01-01-1999"
'   Parms.Header.s_TariffName = "LITECZ02"
'   Parms.Header.s_DetailLines = "01"
'   Parms.Header.s_SingleShipment = "N"
'   Parms.Header.s_IntraState = "N"
   
   Parms.Test = "F"
   Parms.EndDLL = "*"
   Parms.Detail1.s_RecordCode = "02"
   DispCzar
                       
   Call MakeApiCalls(s_ProgPath, s_DataPath, strWeight, strClass, strOrgZip, strDestZip)
            
   Temp = "  " + _
            Right(t_class, 4) + _
            "     " + _
            Right(CStr(strWeight), 6) + _
            "     "
        
    If (Parms.Header.s_ErrorStatus = "    ") Then
         RateShipment = CCur(Right(Parms.Header.s_TotalCharges, 9))
    Else
         RateShipment = -CLng(Parms.Header.s_ErrorStatus)
         'RateShipment = -1
    End If
    
End Function

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
     'Parms.minchgs.mc_ID = "123456789_123456789_123456789_"
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
'Debug.Print "  mc_ID:|"; Parms.minchgs.mc_ID; "|"
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


Public Function GetVersion() As String
    Dim strVer As String
    Dim pos1 As Integer, pos2 As Integer, pos3 As Integer, pos4 As Integer, pos5 As Integer
    strVer = "$Id: RateShipment.cls,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $"
    ' strVer return value should look like:
    ' "CarrierTransit Version 1.4 (2001/01/15 21:11:14) deploy"
    If Len(strVer) > 4 Then
        pos1 = InStr(1, strVer, ",v ") + 3
        pos2 = InStr(pos1 + 1, strVer, " ")
        pos3 = InStr(pos2 + 1, strVer, " ")
        pos4 = InStr(pos3 + 1, strVer, " ")
        pos5 = InStr(pos4 + 1, strVer, " ")
        
    
        strVer = "RateShipment Version " & _
        Mid(strVer, pos1, pos2 - pos1) & _
        " (" & Mid(strVer, pos2 + 1, pos3 - pos2 - 1) & " " & _
        Mid(strVer, pos3 + 1, pos4 - pos3 - 1) & ") " & _
        Mid(strVer, pos4 + 1, pos5 - pos4 - 1)
    Else
        strVer = "Version not available"
    End If
        
    GetVersion = strVer
End Function
