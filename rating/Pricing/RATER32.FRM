VERSION 5.00
Begin VB.Form Form1 
   AutoRedraw      =   -1  'True
   Caption         =   "Form1"
   ClientHeight    =   4344
   ClientLeft      =   456
   ClientTop       =   2292
   ClientWidth     =   6144
   LinkTopic       =   "Form1"
   PaletteMode     =   1  'UseZOrder
   ScaleHeight     =   4344
   ScaleWidth      =   6144
   Begin VB.HScrollBar HScroll1 
      Height          =   252
      Left            =   0
      TabIndex        =   10
      Top             =   4080
      Width           =   5652
   End
   Begin VB.ListBox RateList 
      Height          =   2544
      ItemData        =   "rater32.frx":0000
      Left            =   2520
      List            =   "rater32.frx":0002
      TabIndex        =   9
      Top             =   1200
      Width           =   3015
   End
   Begin VB.TextBox Weight 
      Height          =   288
      Left            =   3360
      MaxLength       =   5
      TabIndex        =   6
      Top             =   240
      Width           =   612
   End
   Begin VB.TextBox Class 
      Height          =   288
      Left            =   2640
      MaxLength       =   4
      TabIndex        =   4
      Top             =   240
      Width           =   492
   End
   Begin VB.TextBox DestZip 
      Height          =   288
      Left            =   1440
      MaxLength       =   5
      TabIndex        =   3
      Top             =   720
      Width           =   852
   End
   Begin VB.TextBox OrgZip 
      BeginProperty Font 
         Name            =   "Small Fonts"
         Size            =   6.6
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   288
      Left            =   1440
      MaxLength       =   5
      TabIndex        =   2
      Top             =   240
      Width           =   852
   End
   Begin VB.Label Label6 
      Caption         =   "Class           Weight"
      Height          =   252
      Left            =   2640
      TabIndex        =   11
      Top             =   840
      Width           =   1332
   End
   Begin VB.Label Label5 
      Caption         =   "Rate    Charges"
      Height          =   252
      Left            =   4200
      TabIndex        =   8
      Top             =   840
      Width           =   1212
   End
   Begin VB.Label Label4 
      Caption         =   "Weight"
      Height          =   252
      Left            =   3360
      TabIndex        =   7
      Top             =   0
      Width           =   612
   End
   Begin VB.Label Label3 
      Caption         =   "Class"
      Height          =   252
      Left            =   2640
      TabIndex        =   5
      Top             =   0
      Width           =   492
   End
   Begin VB.Label Label2 
      Caption         =   "Destination Zip"
      Height          =   288
      Left            =   120
      TabIndex        =   1
      Top             =   720
      Width           =   1092
   End
   Begin VB.Label Label1 
      BackStyle       =   0  'Transparent
      Caption         =   "Origin Zip"
      Height          =   288
      Left            =   480
      TabIndex        =   0
      Top             =   240
      Width           =   732
   End
   Begin VB.Menu Name 
      Caption         =   "&File"
      Begin VB.Menu NEW 
         Caption         =   "  New"
      End
      Begin VB.Menu open 
         Caption         =   "  Open"
      End
      Begin VB.Menu save 
         Caption         =   "  Save"
      End
   End
   Begin VB.Menu EXIT 
      Caption         =   "E&xit"
   End
End
Attribute VB_Name = "Form1"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Dim Display_idx As Integer

Private Sub Class_LostFocus()
   Parms.Detail1.s_Class = Right(Class.Text, 4)
 '  Parms.Detail2.s_Class = Right(Class.Text, 4)
 '  Temp = "   " + Class.Text
 '  RateList.AddItem Temp
End Sub

Private Sub EXIT_Click()
Czar_EndCzar
Unload Form1
End
End Sub

Private Sub Form_Resize()
Form1.Refresh
End Sub

Private Sub NEW_Click()
MsgBox ("Can't create any new files")
End Sub
Private Sub open_Click()
MsgBox ("Can't open any files")
End Sub
Private Sub OrgZip_GotFocus()
' ClearHeader
End Sub
Private Sub OrgZip_KeyPress(KeyAscii As Integer)
x = Chr(KeyAscii)
If x < "0" Or x > "9" Then
   KeyAscii = 0
   End If
If Len(OrgZip.Text) = 4 Then
   Display_idx = -1
   DestZip.SetFocus
   End If
End Sub
Private Sub OrgZip_LostFocus()
Parms.Header.s_OrgZip = OrgZip.Text
End Sub
Private Sub DestZip_KeyPress(KeyAscii As Integer)
x = Chr(KeyAscii)
If x < "0" Or x > "9" Then
   KeyAscii = 0
   End If
If Len(DestZip.Text) = 4 Then
   Class.SetFocus
   End If
End Sub
Private Sub DestZip_LostFocus()
Parms.Header.s_DestZip = DestZip.Text
End Sub
Private Sub Class_GotFocus()
Class.Text = ""
End Sub
Private Sub Class_KeyPress(KeyAscii As Integer)
    KeyAscii = Asc(UCase(Chr(KeyAscii)))
    If KeyAscii = 13 Then
        Weight.SetFocus
    End If
End Sub

Private Sub RateList_Click()
Class.SetFocus
End Sub

Private Sub Weight_GotFocus()
Weight.Text = ""
End Sub
Private Sub Weight_KeyPress(KeyAscii As Integer)
    KeyAscii = Asc(UCase(Chr(KeyAscii)))
    If KeyAscii = 13 Then
        Class.SetFocus
    End If
End Sub

Private Sub Weight_LostFocus()

    Dim Temp As String
    Dim t_class As String
    Dim t_rate As String
    Dim t_charge As String
    Dim n_ErrorNumber As Integer
    Dim x As Integer
    
    Temp = Trim(Weight.Text)
    t_class = Parms.Detail1.s_Class
    Temp = "000000" & Temp
    Parms.Detail1.s_Weight = Right(Temp, 6)
'    Parms.Detail2.s_Weight = Right(Temp, 6)
    
'    n_ErrorNumber = Czar_RateShipment(Parms)
    Call Czar_RateShipment(Parms)
    
    DispCzar
'    x = 0    t_rate = Parms.Detail(x).s_Rate    t_charge = Parms.Detail(x).s_Charge


    Temp = "  " + _
         Right(t_class, 4) + _
         "     " + _
         Right(Weight.Text, 6) + _
         "     "
'        and _ Parms.Detail(0).s_Rate <> "       ")
    If (Parms.Header.s_ErrorStatus = "    ") _
        Then
         Temp = Temp + _
           Right(Parms.Detail1.s_Rate, 7) + _
           "       " + _
           Right(Parms.Detail1.s_Charge, 9)
 
'           Right(Parms.Detail(0).s_Rate, 7) + _           "       " + _           Right(Parms.Detail(0).s_Charge, 9)
     Else
         Temp = Temp + "*** Err " + _
            Parms.Header.s_ErrorStatus + " ***"
     End If
'    RateList.RemoveItem 0
    RateList.AddItem Temp

End Sub


