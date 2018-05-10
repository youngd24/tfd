VERSION 5.00
Begin VB.Form Form1 
   Caption         =   "Form1"
   ClientHeight    =   3195
   ClientLeft      =   1665
   ClientTop       =   1575
   ClientWidth     =   4680
   LinkTopic       =   "Form1"
   ScaleHeight     =   3195
   ScaleWidth      =   4680
   Begin VB.CommandButton Command1 
      Caption         =   "Command1"
      Height          =   495
      Left            =   1800
      TabIndex        =   0
      Top             =   1320
      Width           =   1215
   End
End
Attribute VB_Name = "Form1"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Private Sub Command1_Click()
    
    Dim x As New DigiCarrier.CarrierTransit
    Dim nHandle As Long
    Dim z As String
    
    nHandle = x.ServGd32Initialize()
    y = x.GetTransitDays(nHandle, "RDWY", "LTL", "45440", "60613")
    z = x.GetVersion
    Call x.ServGd32Terminate(nHandle)

End Sub
