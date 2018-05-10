/*  CZERRMSG.H  */
#define   OPNTDA    0x0101
#define   OPNTDS    0x0102
#define   OPNTDX    0x0103
#define   OPNTDZ    0x0104
#define   OPNTDT    0x0105
#define   OPNTDN    0x0106
#define   REDTDS    0x0107
#define   REDTDX    0x0108
#define   REDTDZ    0x0109
#define   REDTDT    0x0110
#define   REDTDN    0x0111
#define   SERTDS    0x0112
#define   SERTDX    0x0113
#define   GETTDZ    0x0114
#define   GETTDT    0x0115
#define   GETTDN    0x0116
#define   IVDORG    0x0117
#define   IVDDST    0x0118
#define   IVDCMB    0x0119
#define   MEMTDA    0x011A
#define   IVDEFC    0x011B
#define   OPNTDV    0x011C
#define   REDTDV    0x011D
#define   SERTDV    0x011E

#define   TOOMANY   0x0199
#define   TARNF     0x0200
#define   MEMORY    0x0201
#define   TYPTAR    0x0202
#define   EFCOPN    0x0203
#define   FACOPN    0x0204
#define   ZRMOPN    0x0205
#define   PNTOPN    0x0206
#define   IDXOPN    0x0207
#define   RTEOPN    0x0208
#define   EFCRED    0x0209
#define   FACRED    0x0210

#define   PNTRED    0x0212
#define   IDXRED    0x0213
#define   RTERED    0x0214
#define   EFCFUL    0x0215
#define   BADORG    0x0216
#define   BADDST    0x0217
#define   BADCMB    0x0218
#define   WTPOPN    0x0219
#define   EXPOPN    0x0220
#define   EBSOPN    0x0221
#define   EMTOPN    0x0222
#define   INVRBN    0x0223
#define   HCY2BIG   0x0224

/* VERRED 0x0225
 | TBLRED 0x0226
 | WGTRED 0x0227
 | CLSRED 0x0228
 | BADVER 0x0229
 */
#define   XCPOPN    0x0229
#define   MINOPN    0x022A
#define   MINRED    0x022B

#define   LALOPN    0x0230
#define   LADOPN    0x0231
#define   BADLAL    0x0232
#define   BADLAD    0x0233
#define   LAL2BIG   0x0234
#define   LAHCNT    0x0235

#define   TTPOPN    0x0240

#define   RBAOPN    0x0250

#define   TRBOPN    0x0260

#define   CFACOPN   0x0270
#define   BIGERR    0x0271
#define   BADTNO    0x0272      // mes 4.00 For a czarlite tariff the table number could not be found in TTNO
#define   PBASOPN   0x0273      // clo 4.2  For USF, opening .O to get percentage basing
#define   PMATOPN   0x0274      // clo 4.2  For USF, opening .J to get percentage matrix
#define   ADJNF     0x0275      // clo 4.2  For USF, did not find the percentage adj in .A
#define   XCPRED    0x0276     // clo 4.3 For NEW USF, could not read .X?? file.
#define   IVDTSD    0x0394
#define   IVDTAR    0x0395
#define   IVDDTE    0x0396

#define   BADCLS    0x0400

typedef struct errm_str
{
  char err_def[8];
  int  err_cd;
  char err_desc[30];
} ERRSTR;

/*

Included for documentation purposes only.  Actual messages may differ.

ERRSTR errm_rec[] =
{
  { "DLLUSE ", 0x010,  "DLL is in use            " },
  { "OPNTDA ", 0x101,  "Can't Open Tar        TDA" },
  { "OPNTDS ", 0x102,  "Can't Open Tar Select TDS" },
  { "OPNTDX ", 0x103,  "Can't Open Tar Except TDX" },
  { "OPNTDZ ", 0x104,  "Can't Open Tar Zip Cd TDZ" },
  { "OPNTDT ", 0x105,  "Can't Open Tar Table  TDT" },
  { "OPNTDN ", 0x106,  "Can't Open Except Nm  TDN" },

  { "REDTDS ", 0x107,  "Can't Read Tar Select TDS" },
  { "REDTDX ", 0x108,  "Can't Read Tar Except TDX" },
  { "REDTDZ ", 0x109,  "Can't Read Tar Zip Cd TDZ" },
  { "REDTDT ", 0x110,  "Can't Read Tar Table  TDT" },
  { "REDTDN ", 0x111,  "Can't Read Except Nm  TDN" },

  { "SERTDS ", 0x112,  "Srch Failed           TDS" },
  { "SERTDX ", 0x113,  "Srch Failed           TDX" },
  { "GETTDZ ", 0x114,  "ZIP Code Not Found in TDZ" },
  { "GETTDT ", 0x115,  "Get Failed            TDT" },
  { "GETTDN ", 0x116,  "Get Failed            TDN" },

  { "IVDORG ", 0x117,  "Invalid Origin Zip Code  " },
  { "IVDDST ", 0x118,  "Invalid Destin Zip Code  " },
  { "IVDCMB ", 0x119,  "Invalid Zip Combination  " },
  { "MEMTDA ", 0x11A,  "Memory allocation err:TDA" },
  { "IVDEFC ", 0x11B,  "Tariff NF in EFCREC.TBL  " },

  { "OPNTDV ", 0x11C,  "Open error            TDV" },
  { "REDTDV ", 0x11D,  "Read error            TDV" },
  { "SERTDV ", 0x11E,  "No Version Rec - Ship Dte" },

  { "123    ", 0x123,  "City/St Must Be Specified" },
  { "124    ", 0x124,  "CTY File Has Been Built  " },
  { "125    ", 0x125,  "Tariff  Must Be Specified" },
  { "126    ", 0x126,  "TRF File Has Been Built  " },
  { "128    ", 0x128,  "Can't Open CTY or TRF FL " },
  { "129    ", 0x129,  "Write Err - CTY or TRF FL" },
  { "FSEARCH", 0x140,  "LSEEK Error              " },
  { "TOOMANY", 0x199,  "Too Many Files Open      " },
  { "TARNF  ", 0x200,  "Tariff Files Not Found   " },

  { "MEMORY ", 0x201,  "Memory error             " },
  { "TYPTAR ", 0x202,  "EFCREC.TBL Has Inval Type" },

  { "EFCOPN ", 0x203,  "Can't Open EFCREC.TBL FL " },
  { "FACOPN ", 0x204,  "Can't Open Tar Factor FL " },
  { "ZRMOPN ", 0x205,  "Can't Open Tar ZIP/RBN FL" },
  { "PNTOPN ", 0x206,  "Can't Open Tar Point File" },
  { "IDXOPN ", 0x207,  "Can't Open Tar Rte FL Idx" },
  { "RTEOPN ", 0x208,  "Can't Open Tar Rate File " },

  { "EFCRED ", 0x209,  "Can't Open EFCREC.TBL FL " },
  { "FACRED ", 0x210,  "Can't Read Tar Factor FL " },
// { "PNXRED ", 0x211,  "Can't Read Tar Pnt FL Idx" },
  { "PNTRED ", 0x212,  "Can't Read Tar Point File" },
  { "IDXRED ", 0x213,  "Can't Read Tar Rte FL Idx" },
  { "RTERED ", 0x214,  "Can't Read Tar Rate File " },

  { "EFCFUL ", 0x215,  "EFCREC.TBL Is Full       " },
  { "BADORG ", 0x216,  "Origin Not in Tariff     " },
  { "BADDST ", 0x217,  "Destin Not in Tariff     " },
  { "BADCMB ", 0x218,  "Combination Not in Tariff" },

  { "WTPOPN ", 0x219,  "Can't Open Wt Type    FL " },
  { "EXPOPN ", 0x220,  "Can't Open EXP        FL " },
  { "EBSOPN ", 0x221,  "Can't Open Lite E Base   " }, // Was VEROPN
  { "EMTOPN ", 0x222,  "Can't Open Lite E Matrix " }, // Was TBLOPN
  { "INVRBN ", 0x223,  "Invalid RBNO             " },
  { "HCY2BIG", 0x224,  "Too many HCITY Entries   " },
// { "VERRED ", 0x225,  "Can't Read Version    FL " },
// { "TBLRED ", 0x226,  "Can't Read TBL        FL " },
// { "WGTRED ", 0x227,  "Can't Read WGT        Idx" },
// { "CLSRED ", 0x228,  "Can't Read CLS       File" },
  { "XCPOPN ", 0x229,  "Can't Open Exception  FL " },
  { "MINOPN ", 0x22A,  "Can't Open MIN        FL " },
  { "MINRED ", 0x22B,  "Can't Read MIN        FL " },
  { "LALOPN ", 0x230,  "Can't Open Lane Adj   L??" },
  { "LADOPN ", 0x231,  "Can't Open Lane Adj   A??" },
  { "BADLAL ", 0x232,  "Read error Lane Adj   L??" },
  { "BADLAD ", 0x233,  "Read error Lane Adj   A??" },
  { "LAL2BIG", 0x234,  "Lane Adj too big         " },
  { "LAHCNT ", 0x235,  "More than max LA hits    " },
  { "TTPOPN ", 0x240,  "Can't Open CZAR.TTP      " },
  { "RBAOPN ", 0x250,  "Can't Open RBA           " },
  { "TRBOPN ", 0x260,  "Can't Open RBNO Addons   " },
  { "CFACOPN", 0x270,  "Can't Open C Cls Factors " },
  { "380    ", 0x380,  "Hdr Rec Cd Not Eq '01'   " },
  { "381    ", 0x381,  "Input Rec Length Invalid " },
  { "382    ", 0x382,  "Hdr Detail Cnt Invalid   " },
  { "383    ", 0x383,  "Hdr Detail Cnt >20       " },
  { "390    ", 0x390,  "Dtl Rec Cd Not '02'      " },
  { "391    ", 0x391,  "Hdr Detail Cnt Too High  " },
  { "IVDTSD ", 0x394,  "TarName+Ship Dt Not Found" },
  { "IVDTAR ", 0x395,  "Invalid Tariff Specified " },
  { "396    ", 0x396,  "Invalid Shipment Date    " },
  { "BADCLS ", 0x400,  "Dtl Rec #xx - Inval Class" },
  { "9999   ", 0x9999, "Flagged as Ignore/Delete " },
  { "ffff   ", 0xffff, "**Unknown Error.........." }
};
*/

