VERSION 1.0 CLASS
BEGIN
  MultiUse = -1  'True
  Persistable = 0  'NotPersistable
  DataBindingBehavior = 0  'vbNone
  DataSourceBehavior  = 0  'vbNone
  MTSTransactionMode  = 0  'NotAnMTSObject
END
Attribute VB_Name = "CarrierTransit"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = True
Attribute VB_PredeclaredId = False
Attribute VB_Exposed = True
Option Explicit
Dim bLoadOK As Long

Public Function GetTransitDays(ByVal nHandle As Long, sSCAC As String, ByVal sType As String, sOrgZip As String, sDestZip As String) As Integer
    'Declarations
    Dim sOrgTermCode As String
    Dim sDestTermCode As String
    Dim nDays As Integer
    Dim nCarrIndex As Long
    Dim lret As Long
    
    On Error GoTo Errorhandler
    
    nSgDLLhandle = nHandle
    'nSgDLLhandle = ServGd32Initialize ' To expose ServGd32Initialize
    If nSgDLLhandle = 0 Then Exit Function
    
    'set the data path
    bLoadOK = ServGd32SetDataPath(App.Path)
    If bLoadOK <> 1 Then
       GetTransitDays = bLoadOK
       Exit Function
    End If
        
    GetTransitDays = 0
    
    nCarrIndex = ServGd32GetCarrierInfo(sSCAC, sType)
    
    If nCarrIndex = -1 Then
        GetTransitDays = -1
        Exit Function
    End If
    
    'sTermCode = ServGd32GetTerminalDetail(vCarrierIndex, sOrgZip, sDestZip)
    sOrgTermCode = ServGd32GetTerminalDetail(nCarrIndex, sOrgZip)
    If sOrgTermCode = "" Then
        GetTransitDays = -200 ' Invalid Origination zip code
        Exit Function
    End If
      
    sDestTermCode = ServGd32GetTerminalDetail(nCarrIndex, sDestZip)
    If sDestTermCode = "" Then
        GetTransitDays = -201 ' Invalid destination zip code
        Exit Function
    End If
    
    'nDays = ServGd32GetTermToTermDays(vCarrierIndex, sTermCode, sOrgZip, sDestZip)
    nDays = ServGd32GetTermToTermDays(nCarrIndex, sOrgTermCode, sDestTermCode)
    GetTransitDays = nDays
    Exit Function
    
Errorhandler:
        'GetTransitDays = -Err.Number
        'GetTransitDays = -1
        lret = AppError("GetTransitDays")
        GetTransitDays = -lret
End Function

' ********** functions in ServGd32 dll **********

Public Function ServGd32Initialize() As Long

Dim lret As Long
'*************************************************************************************
'
' Purpose : Initialize a new session of the ServGd32 DLL.
'
'*************************************************************************************
  
  On Error GoTo Errorhandler
  
  ServGd32Initialize = 0
  
  'let the DLL know who is calling it
  SG.InitInfo.Code = "Customer"
  
  'initialize servgd32 dll
  ServGd32Initialize = ServGd_Initialize(SG)
  
  'DLL error occurred
  If SG.ErrorCode >= cnstMinSGdllErrorNo And SG.ErrorCode <= cnstMaxSGdllErrorNo Then
    Err.Raise vbObjectError + cnstServGd32DLLerror + SG.ErrorCode
  End If
  
  Exit Function
    
Errorhandler:
  lret = AppError("ServGd32Initialize")
  ServGd32Initialize = -lret
  'ServGd32Initialize = 0
  'AppError "ServGd32Initialize"
  
  
End Function
Public Sub ServGd32Terminate(ByVal nHandle)

'*************************************************************************************
'
' Purpose : Terminates the current session of the ServGd32 DLL.
'
'*************************************************************************************

    
  On Error GoTo Errorhandler
  Call ServGd_Terminate(nHandle)
  
Errorhandler:
   
    
  Exit Sub
End Sub
Public Function ServGd32SetDataPath(ByVal sDataDirectory) As Long

Dim lret As Long
'*************************************************************************************
'
' Purpose : Sets / Changes the current data path
'
'*************************************************************************************

  On Error GoTo Errorhandler
  
  ServGd32SetDataPath = -1
  
  'sDataDirectory should not end with a "\"
  If Right$(sDataDirectory, 1) = "\" Then
    sDataDirectory = Left$(sDataDirectory, Len(sDataDirectory) - 1)
  End If
  
  ServGd_Set_SGDataPath SG, sDataDirectory, nSgDLLhandle
  
  If SG.ErrorCode >= cnstMinSGdllErrorNo And SG.ErrorCode <= cnstMaxSGdllErrorNo Then
    'warn the user the data path selected has expired data
    If SG.ErrorCode = Val(SG.DataInfo.DataExpErrorCode) Then
        Err.Raise vbObjectError + cnstServGd32DLLerror + SG.ErrorCode
    'DLL error occurred
    Else
        Err.Raise vbObjectError + cnstServGd32DLLerror + SG.ErrorCode
    End If
  End If
  
  ServGd32SetDataPath = 1
  
  Exit Function

Errorhandler:
  'ServGd32SetDataPath = False
  'AppError "ServGd32SetDataPath"
  lret = AppError("ServGd32SetDataPath")
  ServGd32SetDataPath = -lret
  
End Function


Public Function GetVersion() As String
    Dim strVer As String
    Dim pos1 As Integer, pos2 As Integer, pos3 As Integer, pos4 As Integer, pos5 As Integer
    strVer = "$Id: CarrierTransit.cls,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $"
    ' strVer return value should look like:
    ' "CarrierTransit Version 1.4 (2001/01/15 21:11:14) deploy"
    pos1 = InStr(1, strVer, ",v ") + 3
    pos2 = InStr(pos1 + 1, strVer, " ")
    pos3 = InStr(pos2 + 1, strVer, " ")
    pos4 = InStr(pos3 + 1, strVer, " ")
    pos5 = InStr(pos4 + 1, strVer, " ")
        
    'Mid(strVer,,)
    strVer = "CarrierTransit Version " & _
        Mid(strVer, pos1, pos2 - pos1) & _
        " (" & Mid(strVer, pos2 + 1, pos3 - pos2 - 1) & " " & _
        Mid(strVer, pos3 + 1, pos4 - pos3 - 1) & ") " & _
        Mid(strVer, pos4 + 1, pos5 - pos4 - 1)
        
    GetVersion = strVer
End Function
