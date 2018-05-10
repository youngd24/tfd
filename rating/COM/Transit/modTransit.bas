Attribute VB_Name = "modTransit"
Option Explicit

Public Const cnstServGd32DLLerror = 1100
Public Const cnstMinSGdllErrorNo = 10
Public Const cnstMaxSGdllErrorNo = 99

'set >= to the maximum number of carriers in the data set
Public Const cnstMaxCarriers = 100
Public Const cnstMaxCarrierTypes = 10

'set this = to the return value of the ServGd32Initialize function
Public nSgDLLhandle As Long

Type InitInfo
  Flag As String * 10
  Code As String * 10
End Type

Type DataInfo
  DataExpErrorCode As String * 3
  f1 As String * 1
  DataRelease As String * 4
  f2 As String * 1
  DLLVersion As String * 3
  f3 As String * 1
  ExpirationDate As String * 10
  f4 As String * 1
  DataPath As String * 255
End Type

Type Service
    SCAC As String * 4
    f1 As String * 1
    CarrierName As String * 50
    f2 As String * 1
    CarrierType As String * 20
    f3 As String * 1
    OrigTermCode As String * 5
    f4 As String * 1
    DestTermCode As String * 5
    f5 As String * 1
    OrigServType As String * 1
    f6 As String * 1
    DestServType As String * 1
    f7 As String * 1
    ServiceType As String * 1
    f8 As String * 1
    Days As String * 2
    f9 As String * 1
    OrigZip As String * 6
    f10 As String * 1
    DestZip As String * 6
    f11 As String * 1
End Type

'used in terminfo and carrinfo
Type ContactInfo
    Address1 As String * 50
    f1 As String * 1
    Address2 As String * 50
    f2 As String * 1
    City As String * 30
    f3 As String * 1
    State As String * 2
    f4 As String * 1
    ZipCode As String * 10
    f5 As String * 1
    PhoneNo As String * 12
    f6 As String * 1
    FreePhone As String * 12
    f7 As String * 1
    FaxNo As String * 12
    f8 As String * 1
    EMail As String * 100
    f9 As String * 1
    ContactName As String * 50
    f10 As String * 1
    ContactTitle As String * 50
    f11 As String * 1
End Type

Type TermInfo
    TermIndex As String * 11
    Zip As String * 6
    f1 As String * 1
    TermName As String * 50
    f2 As String * 1
    TermCode As String * 5
    f3 As String * 1
    ContactInfo As ContactInfo
End Type

Type CarrInfo
    SCAC As String * 4
    f1 As String * 1
    CarrName As String * 50
    f2 As String * 1
    CarrType As String * 20
    f3 As String * 1
    ContactInfo As ContactInfo
    DataDate As String * 10
    f4 As String * 1
End Type

Type TermToTerm
    InboundDays As String * 2
    f1 As String * 1
    OutboundDays As String * 2
    f2 As String * 1
    TermOne As String * 5
    f3 As String * 1
    TermTwo As String * 5
    f4 As String * 1
    TermIndexOne As String * 11
    TermIndexTwo As String * 11
    NumOfTerms As String * 11
    TermIndexStart As String * 11
End Type

Type AvailableCarriers
    SCAC As String * 4
    f1 As String * 1
    Type As String * 20
    f2 As String * 1
End Type

Type SG
    ErrorCode As Integer
    CarrierIndex As Integer
    Counter As Integer
    InitInfo As InitInfo
    DataInfo As DataInfo
    AvailableCarriers As AvailableCarriers
    Service As Service
    CarrInfo As CarrInfo
    TermInfo As TermInfo
    TermToTerm As TermToTerm
End Type
Public SG As SG
Dim Path As String



' ********** functions in ServGd32 dll **********
Declare Function ServGd_Initialize Lib "C:\digiship\rating\transit\ServGd32.dll" _
        (ByRef SG As SG) As Long
Declare Function ServGd_Terminate Lib "C:\digiship\rating\transit\ServGd32.dll" _
        (ByVal nHandle As Long) As Boolean
Declare Function ServGd_GetTermToTermDays Lib "C:\digiship\rating\transit\ServGd32.dll" _
        (ByRef SG As SG, ByVal nHandle As Long) As Boolean
Declare Function ServGd_GetTerminalDetail Lib "C:\digiship\rating\transit\ServGd32.dll" _
        (ByRef SG As SG, ByVal nHandle As Long) As Boolean
Declare Function ServGd_Set_SGDataPath Lib "C:\digiship\rating\transit\ServGd32.dll" _
        (ByRef SG As SG, ByVal sDataPath As String, ByVal nHandle As Long) As Boolean
Declare Function ServGd_GetCarriers Lib "C:\digiship\rating\transit\ServGd32.dll" _
        (ByRef SG As SG, ByVal nHandle As Long) As Boolean

        

' ********************************************************************************
' This is a Visual Basic module. Most all of the string type members use a trailing
' string filler for a length of 1.  This is to allow C++ to attach a null terminator
' on the end of each string, which the VB program will ignore.
' ********************************************************************************

' use this # to when raising errors returned by the dll.
' this will keep the ServGuide dll errors seperate from other errors

Public Function ServGd32GetTermToTermDays(ByVal nCarrierIndex As Integer, ByVal sOrgTermCode As String, ByVal sDestTermCode As String) As Long

'*************************************************************************************
'
' Purpose : To display the terminal to terminal days for the selected carrier and terminal.
'
' Comment: 1) ServGd32GetTermToTermDays calls the ServGd32 dll to retrieve the terminal
'             to terminal days for the selected carrier and terminal.
'             The carrier number is passed by nCarrierIndex.
'
'          2) CurrentControl is the control the returned days will be displayed in.
'
'          3) ServGd32GetTermToTermDays returns the number of term to term items returned
'             by the DLL.  A -1 is returned if an error occurs.
'
'*************************************************************************************
  
  On Error GoTo Errorhandler
  
  Dim bResult As Boolean
  Dim nCnt As Integer
  Dim nVal As String
    
  ServGd32GetTermToTermDays = 0
  
'confirm the control type passed to this function is valid
'  If Not (TypeOf CurrentControl Is ListBox Or TypeOf CurrentControl Is ComboBox) Then
'    MsgBox "Invalid control type passed ServGd32GetTermToTermDays"
'    Exit Function
'  End If
  
  SG.CarrierIndex = nCarrierIndex
  SG.TermToTerm.TermOne = sOrgTermCode
  
  'Rajan
  SG.TermToTerm.TermTwo = sDestTermCode
  
  'CurrentControl.Clear
  bResult = True
  
  bResult = ServGd_GetTermToTermDays(SG, nSgDLLhandle)
  
    Do While bResult
        If (SG.TermToTerm.TermTwo = sDestTermCode) Then
            nVal = Val(SG.TermToTerm.InboundDays)
        End If
        bResult = ServGd_GetTermToTermDays(SG, nSgDLLhandle)
        If (SG.TermToTerm.TermTwo <> sDestTermCode) Then
            'process terminated successfully
            If SG.ErrorCode = 1 Then Exit Do
        
            'carrier does not service this lane
            If SG.ErrorCode = 101 Then
                'do nothing
            'DLL error occurred
            ElseIf SG.ErrorCode >= cnstMinSGdllErrorNo And SG.ErrorCode <= cnstMaxSGdllErrorNo Then
                Err.Raise vbObjectError + cnstServGd32DLLerror + SG.ErrorCode
                nVal = -1
            'successfull call to DLL, display the results
            End If
          Else
            nVal = Val(SG.TermToTerm.InboundDays)
          End If
    Loop
  
  
  ServGd32GetTermToTermDays = CLng(nVal)
  Exit Function

Errorhandler:
  ServGd32GetTermToTermDays = -1
  AppError "ServGd32GetTermToTermDays"
  
End Function

Public Function ServGd32GetTerminalDetail(ByVal nCarrierIndex As Integer, ByVal sZipCode As String) As String

'*************************************************************************************
'
' Purpose :
'
' Comment: 1) ServGd32GetTerminalDetail retrieves the terminal detail information.
'
'*************************************************************************************

  Dim x As Integer
  Dim sTermCode As String
  
  SG.CarrierIndex = nCarrierIndex
    
  'call service guide dll, pass the terminal code to get the detail terminal information
'  SG.TermInfo.TermCode = sTermCode
  SG.TermInfo.Zip = sZipCode
  SG.TermInfo.TermCode = ""
  ServGd_GetTerminalDetail SG, nSgDLLhandle
  
  'successfull call to DLL, display the results
  If SG.ErrorCode = 1 Then
    sTermCode = SG.TermInfo.TermCode ' Rajan
  'DLL error occurred
  ElseIf SG.ErrorCode >= cnstMinSGdllErrorNo And SG.ErrorCode <= cnstMaxSGdllErrorNo Then
    Err.Raise vbObjectError + cnstServGd32DLLerror + SG.ErrorCode
  End If
  ServGd32GetTerminalDetail = sTermCode
  Exit Function
  
Errorhandler:
  ServGd32GetTerminalDetail = ""
  AppError "ServGd32GetTerminalDetail"
  
End Function

Public Function ServGd32GetCarrierInfo(ByVal sSCAC As String, ByVal sType As String) As Long

'*************************************************************************************
'
' Purpose : To get the carrier index from SCAC Code
'
' Comment: 1) ShowCarriers calls the ServGd32 dll to retrieve the carrier information.
'
'
'*************************************************************************************
  
  
  Dim nCarrIndex As Long
  
  ServGd32GetCarrierInfo = 0
  'confirm the sSCAC passed to this function is valid
  'If Len(sSCAC) > Len(SG.CarrInfo.SCAC) Or Trim$(sSCAC) = "" Then
  '  MsgBox "Invalid SCAC passed ServGd32GetCarrierInfo"
  '  Exit Function
  'End If
  
  SG.CarrierIndex = 0
  SG.CarrInfo.SCAC = Trim$(sSCAC)
  SG.CarrInfo.CarrType = sType
      
  'call the ServGd32 dll to get the current carriers detail information
  'display the fields that have information in them
  ServGd_GetCarriers SG, nSgDLLhandle
    
  'successfull call to DLL, display the results
  If SG.ErrorCode = 1 Then
    'carrier index
    If Trim$(SG.CarrierIndex) <> "" Then
        nCarrIndex = CLng(Trim$(SG.CarrierIndex))
    End If
  
  'DLL error occurred
  ElseIf SG.ErrorCode >= cnstMinSGdllErrorNo And SG.ErrorCode <= cnstMaxSGdllErrorNo Then
    Err.Raise vbObjectError + cnstServGd32DLLerror + SG.ErrorCode
  End If
  
  ServGd32GetCarrierInfo = nCarrIndex
  
  Exit Function
  
Errorhandler:
  ServGd32GetCarrierInfo = -1
  AppError "ServGd32GetCarrierInfo"
  
End Function

