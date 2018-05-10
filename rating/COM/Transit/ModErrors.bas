Attribute VB_Name = "ModErrors"
Option Explicit

'*************************************************************************************
'
' Module : modErrors
'
' Purpose : Common error handler
'
'*************************************************************************************

Dim nErrorNumber As Long
Dim sErrorDescription As String
Dim sErrorMessage As String

Public Function AppError(sLocation As String) As Long

  AppError = 0
  sErrorMessage = ""
  sErrorDescription = ""
  nErrorNumber = 0
    
  'set the error number
  nErrorNumber = Err.Number

  'set the error description
  SetDescription
  
  AppError = nErrorNumber
    
  'display the error message on the screen
  'DisplayError (sLocation)

  'clear the system Err object
  Err.Clear

End Function

Private Sub SetDescription()
  
  If nErrorNumber <> 0 Then
    'raised error
    If nErrorNumber < 0 Then
         
         nErrorNumber = nErrorNumber - vbObjectError
         
'         Select Case nErrorNumber
'            'set the ServGd32 dll error description, '1100
'            Case 1110
'                sErrorDescription = "The DLL was unable to initialize properly." & vbCrLf & _
'                                    Space(4) & vbTab & "Confirm the data has not expired."
'            Case 1111
'                sErrorDescription = "The data in the current data directory has expired." & vbCrLf & _
'                                    Space(4) & vbTab & "Change the data directory to a valid " & _
'                                    "data set, or " & vbCrLf & Space(4) & vbTab & _
'                                    "contact SMC sales to obtain an updated data set."
'            Case 1112
'                sErrorDescription = "The data in the current data directory" & vbCrLf & _
'                                    Space(4) & vbTab & "can not be located or is incompatible" & vbCrLf & _
'                                    Space(4) & vbTab & "with the current version of the DLL."
'           Case 1113
'                sErrorDescription = "Invalid carrier passed to the dll"
'
'            Case 1114
'                sErrorDescription = "Invalid handle passed to the dll"
'
'            Case 1115
'                sErrorDescription = "Invalid carrier type passed to the dll"
'
'            'ServGd32 dll file errors
'            Case 1131
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                    vbTab & "Unable to open data file CarrList"
'            Case 1132
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                    vbTab & "Unable to read data file CarrList"
'           Case 1133
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                    vbTab & "Unable to open data file ServZips"
'            Case 1134
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                    vbTab & "Unable to read data file ServZips"
'            Case 1135
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                    vbTab & "Unable to open data file ServTerm"
'            Case 1136
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                    vbTab & "Unable to read data file ServTerm"
'            Case 1137
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                    vbTab & "Unable to open data file ServMatx"
'            Case 1138
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                    vbTab & "Unable to read data file ServMatx"
'            Case 1139
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                     vbTab & "Unable to open data file ServCarr"
'            Case 1140
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                    vbTab & "Unable to read data file ServCarr"
'            Case 1141
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                    vbTab & "Unable to open data file TypeList"
'            Case 1142
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                    vbTab & "Unable to read data file TypeList"
'            Case 1143
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                    vbTab & "Unable to open data file Carriers"
'            Case 1144
'                sErrorDescription = "ServGD32.DLL error " & vbCrLf & Space(4) & _
'                                    vbTab & "Unable to read data file Carriers"
'
'        End Select
'
'    'vb error
'   Else
'        sErrorDescription = Err.Description
    End If
'
'    If Trim$(sErrorDescription) = "" Then sErrorDescription = "Unknown Error"
'
  End If
    
End Sub

Private Sub DisplayError(sModule As String)

  'display the error to the user
  'sLocation indicates module where error occured
      
  'assign error message string variable value
  sErrorMessage = "The following error occurred:" & vbCrLf & vbCrLf
  
  If nErrorNumber <> 0 Then
    If nErrorNumber < 0 Then
      sErrorMessage = sErrorMessage & "Error:" & vbTab & Str(nErrorNumber - vbObjectError) & vbCrLf & vbCrLf
    Else
      sErrorMessage = sErrorMessage & "Error:" & vbTab & Str(nErrorNumber) & vbCrLf & vbCrLf
    End If
  End If
  
  sErrorMessage = sErrorMessage & "Msg:" & vbTab & sErrorDescription & vbCrLf & vbCrLf
  sErrorMessage = sErrorMessage & "Module:" & vbTab & sModule

  'display the error to the user's screen
  'MsgBox sErrorMessage, vbCritical + vbOKOnly, "Application Error"
    
End Sub

