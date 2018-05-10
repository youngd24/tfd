// Test Dll
/*    This program demonstrates the CZAR DLL
 *    Usage: testdll input<ENTER>
 *    It reads an input file, specified by param 1, containing
 *    one or more sets of header and detail records,
 *    calculates rates and charges, and updates the input file.
 */
                                    /*   define maximum # detail lines */
//#define LNE_DIM 40
                                 /*  define input detail record length */
                                 /*  (could also be 30) */
#define DTL_LEN   29
#include <windows.h>
#include <stdio.h>
#include <stdlib.h>
#include <direct.h>
#include <fcntl.h>
#include <math.h>
#include <string.h>
#include <search.h>
#include <errno.h>
#include "datatype.h"
#include "common.h"
#include "versions.h"
#include "CZARDLL.h"



#define EXPAND_CNT 175

CZARDLL		Parm;

FILE*	bfptr;

short	czarstat;
char	file_name[_MAX_PATH];
char	ratepath[_MAX_PATH]   = "";		// tariff data path
char	arg_in[_MAX_PATH];
long	header_offset;					// offset into file of header record
long	detail_offset;
int		rateline_cnt;
int		no_items;						// number of commodities on bill
int		dsc_len;						// len of discount rec
int		test_fp;
short	prog_id;
char	prog_nm[9];

int		BillCount = 0;

ERTE_REC		erte_rec;
RATE_LINE		rate_line;
MINCHG_REC		mc_rec;

/*
 *    Local & called subroutine declarations.
 */

void CopyAfter(void);
//   void DispErr(int error_code);
int ReadBill(FILE* fp);
void WriteBill(FILE* fp);
void io_error(char *fn);
int GetBill(FILE* fp);
void PutBill(FILE* fp);


//
//    Main subroutine.
//
void main(int argc, char *argv[])
{
	typedef struct
	{
		char filler;
		char multi_1;
		char err_fl;
		char comp_part;

	} PROGMODE;

	INT		return_code, i; 
	char	*testpath;

	return_code=0;
	return_code = 0;
	if (argc < 1 )
	{
		printf("Missing Name to Rate\n");
		exit(-1);
	}

	memset(file_name, 0, sizeof(file_name));
	for(i = 1; i < argc; i++)
	{
		if(argv[i][0] == '/' || argv[i][0] == '-')
			strcpy(arg_in, argv[i]+1);
		else
			strcpy(arg_in, argv[i]);

		if (strnicmp(arg_in, "DATA=", 5) == 0)
		{
			strcpy(ratepath, arg_in + 5);
			if (ratepath[strlen(ratepath) - 1] != '\\')
				strcat(ratepath, "\\");
		}
		else
			strcpy(file_name, arg_in);
	}
	 
	if(strlen(file_name) < 1)
	{   
		printf("File Name to Rate Not Found\n"); 
		exit(-1);
	}

	if (strlen(ratepath) < 1)
	{
		_getcwd(ratepath, _MAX_PATH);
		if (ratepath[strlen(ratepath) - 1] != '\\')
			strcat(ratepath, "\\");
	}

	if ((testpath = getenv("CZARDATA")) != NULL )
	{
		strcpy(ratepath, testpath);
		if (ratepath[strlen(ratepath) - 1] != '\\')
			strcat(ratepath, "\\");
	}       

	if ((return_code = Czar_InitializeCzar(ratepath)) != 0)       
	{
//		DispErr(return_code);
		exit(-1);
	}

	return_code = Czar_Set_Czardata(ratepath);
	if (return_code != 0 && return_code != 1) 
	{	   	  
//		DispErr(return_code);
		exit(-1); 
	}

//	Display versions
	VERSIONS ver_info;
	Czar_disp_ver(&ver_info);              

//	Open input file.
	_fmode = _O_TEXT;
//	Czar_SetFileName(file_name);
	if ((bfptr = fopen(file_name,"r+b")) == NULL)
	{
		printf("Cannot Open File to Rate\n");
		exit(-1);
	}

	memset(&Parm, 0, sizeof(CZARDLL));
	while(ReadBill(bfptr))               // Fill in header & detail
	{
		Parm.Test = 'F';
		memcpy(Parm.fill1, "***", 3);
		memcpy(Parm.fill2, "***", 3); 
		Parm.EndDLL = '*';

		if (czarstat == 0)
		{
			PROGMODE* pProgMode = (PROGMODE*)Parm.header.program_mode;
			if (pProgMode->multi_1 == ' ')
				pProgMode->multi_1 = 'M';

			if (pProgMode->err_fl == ' ')
				pProgMode->err_fl = 'E';

			if (pProgMode->comp_part == ' ')
				pProgMode->comp_part = '1';

			if (Parm.header.intra_state[0] == ' ')
				Parm.header.intra_state[0]  = 'N';

			if (Parm.header.single_shipment[0] == ' ')
				Parm.header.single_shipment[0] = 'N';

//			memcpy(Parm.header.orgzip, "01001", 5);
//			memcpy(Parm.header.dstzip, "40205", 5);
//			memcpy(Parm.header.tariff_name, "TNT50202", 8);
//			memcpy(Parm.header.shipment_date, "01011995", 8);

//			char	orgpnt[20], orgzip[6];
//			char	dstpnt[20], dstzip[6];

//			memset(orgpnt, 0, sizeof(orgpnt));
//			memcpy(orgzip, Parm.header.orgzip, sizeof(Parm.header.orgzip));
//			memset(dstpnt, 0, sizeof(dstpnt));
//			memcpy(dstzip, Parm.header.dstzip, sizeof(Parm.header.dstzip));
//			Czar_GetState(orgpnt, orgzip);
//			Czar_GetState(dstpnt, dstzip);

/*
			Code used in testing dont delete.
			GETTABLES	g;
			
			if (Parm.header.tariff_name[0] != ' ')
			{
				memcpy(g.ShipDate, Parm.header.shipment_date, 10);
				memcpy(g.TariffName, Parm.header.tariff_name, 8);
				Czar_GetTables(&g);
			}
*/
			Czar_RateShipment(&Parm);  // call DLL
			BillCount++;
//			printf("%d\n", BillCount);
			sscanf(Parm.header.error_status, "%04x", &czarstat);
		}
		else
		{
			char	buf[5];
			sprintf(buf, "%04x", czarstat);
			memcpy(Parm.header.error_status, buf, 4);
		}

		if (czarstat != 0)
		{
			// do some error processing here
		}

		WriteBill(bfptr);
		if (Parm.header.program_mode[1] == 'S')
			break;
	}

	fclose(bfptr);
	Czar_EndCzar();
	printf("Process Completed\n");
	exit(0);

}

/*
void DispErr(int error_code)
{
	int		x;
	char	d_no[3];

	x = 0;
	do
	{
		if (error_code == errm_rec[x].err_cd)
			break;
		// if .err_cd is class err & error_code is class err, setup msg
		if ((error_code > 0x400 && error_code < 0x499) && errm_rec[x].err_cd == 0x400)
		{
			sprintf(d_no, "%2.2x", error_code - 0x400);
			strncpy(&errm_rec[x].err_desc[9], d_no, 2);
			break;
		}
		x++;
	} while(errm_rec[x].err_cd != -1);

	// if .err_cd is Cnt > LNE_DIM, setup msg
	if (errm_rec[x].err_cd == 0x383)
	{
		sprintf(d_no, "%2.2d", LNE_DIM);
		strncpy(&errm_rec[x].err_desc[16], d_no, 2);
	}

//   printf("\n %-16.16s %4.4x:%25.25s \n",
//        header.pro_number, error_code, errm_rec[x].err_desc);
//clo 3.50
}
*/

int ReadBill(FILE* fp)
{
	czarstat = 0;

	BillCount++;
	if (GetBill(fp) != 1)
	{
		return 0;
	}

	return 1;
}

void WriteBill(FILE* fp)
{
    PutBill(fp);
}

void io_error(char *fn)
{ 
	printf("File %-s : ", fn);
	switch (errno)
	{
	case EACCES :
		printf("R/O or locked against writing\n");
		break;
	case EBADF :
		printf("Invalid handle\n");
		break;
	case ENOSPC :
		printf("No space left\n");
		break;
	case EEXIST :
		printf("Already exists\n");
		break;
	case EMFILE :
		printf("Too many files open\n");
		break;
	case ENOENT :
		printf("Not found\n");
		break;
	} 

	exit(errno);
}



       INT   b03_len;
       INT   b04_len;
       INT   b05_len;

extern CHAR *dsc_ptr;		  // ~discount addr from parent
extern long  header_offset;
extern int   no_items;		  //  number of commodities on bill
       long  prev_file_pos;

int   bdate(char *dd, void *d);
INT   chk_next(void);
void  cnvrt_err(char *err_no);
void  disp_dtl_after(void);
void  disp_dtl_before(void);
void  disp_hdr_after(void);
void  disp_hdr_before(void);
BOOL  disp_response(void);
void  io_error( char *fn);
void  unknown_rec_err(void);

static long Position = 0L;
static long CurrentPosition = 0L;
       long RecordCount = 0L;
       long OneRecordNumber = 0L;
static long DetailPosition;
static long DiscountPosition;
static int  DetailCount;
static long IndDiscountPosition;
static long Record03Position;
static long Record04Position;
static long Record05Position;
static BOOL bEndOfFile = FALSE;

/*
	typedef struct {
	  CHAR      Test[1];
	  HEADER    header;              // shipment header 
	  DETAIL    detail_arr[20]; // shipment detail lines 
	  WBDISC_IN wbdisc_in;
	  WBDISC_IN indir_in;
	  CHAR		fill1[3];
	  ULONG     expanded_rates[175];
	  ERTESUBS  ert_subs;
	  MINCHGS   minchgs;
	  CHAR		fill2[3];
	  CHAR      EndDLL[1];
	} CZARDLL;
*/

int GetBill(FILE* fp)
{
    int     ReturnCode;
    char    buffer[400];
    int     nRecordCode;
    long    nRecordLength;
    long    nBytesRead;
    long    RecordPosition;

    ReturnCode = 1;	// Assume everything will be ok to start

    if (bEndOfFile)
		return 0;

    CurrentPosition = Position;
    fseek(fp, Position, SEEK_SET);
    RecordPosition = ftell(fp);
    if (fgets(buffer, sizeof(buffer), fp) == NULL)
    {
		bEndOfFile = TRUE;
		return 0;
    }
    buffer[strlen(buffer) - 2] = '\0';
    nBytesRead = strlen(buffer);
    if (nBytesRead <= 1)
    {
		bEndOfFile = TRUE;
		return 0;
    }

    RecordCount++;
    OneRecordNumber = RecordCount;

    nRecordCode = 1;
    nRecordLength = 264;

    if (memcmp(buffer, "01", 2) != 0)
    {
		printf("Was expecting an ""01"" record. Found an %-2.2s record.\r\n", buffer);
		printf("Processing ceases.\r\n");
		exit(0);
    }

    memcpy(&Parm.header, buffer, sizeof(HEADER));

#ifdef M_I86
    sscanf(Parm.header.detail_lines, "%2d",  &no_items);
#else
    sscanf(Parm.header.detail_lines, "%2hd", &no_items);
#endif

    DetailPosition = 0;
    DiscountPosition = 0;
    IndDiscountPosition = 0;
    Record03Position = 0;
    Record04Position = 0;
    Record05Position = 0;
    detail_offset = 0;
    b03_len = b04_len = b05_len = rateline_cnt = 0;
    DetailCount = 0;
    nRecordCode = 0;
    while(nRecordCode != 1 && !bEndOfFile)
    {
		RecordPosition = ftell(fp);
		if (fgets(buffer, sizeof(buffer), fp) == NULL)
		{
			bEndOfFile = TRUE;
			return 1;
		}
		buffer[strlen(buffer) - 2] = '\0';
		nBytesRead = strlen(buffer);
		if (nBytesRead <= 1)
		{
			bEndOfFile = TRUE;
			ReturnCode = 1;
			break;
		}

		RecordCount++;

		nRecordCode = 0;
		if (memcmp(buffer, "01", 2) == 0)  nRecordCode = 1;
		if (memcmp(buffer, "02", 2) == 0)  nRecordCode = 2;
		if (memcmp(buffer, "03", 2) == 0)  nRecordCode = 3;
		if (memcmp(buffer, "04", 2) == 0)  nRecordCode = 4;
		if (memcmp(buffer, "05", 2) == 0)  nRecordCode = 5;
		if (memcmp(buffer, "D%", 2) == 0)  nRecordCode = 6;
		if (memcmp(buffer, "I%", 2) == 0)  nRecordCode = 7;

		switch (nRecordCode)
		{
			case 1:   // reclen = 266
				nRecordLength = 264;
				Position = RecordPosition;
				RecordCount--;
				break;
			case 2:
				if (DetailPosition == 0) DetailPosition = RecordPosition;
				nRecordLength = 29;
				if (DetailCount < LNE_DIM)
					strcpy((char*)&Parm.detail_arr[DetailCount++], buffer);
				break;
			case 3:
				if (Record03Position == 0) Record03Position = RecordPosition;
				nRecordLength = 168;
				strcpy((char*)&Parm.ert_subs, buffer);
				b03_len = strlen(buffer);
				break;
			case 4:
				if (Record04Position == 0)
				{
					Record04Position = RecordPosition;
					b04_len = strlen(buffer);
				}
				nRecordLength = 86;
				strcpy((char*)&Parm.expanded_rates, buffer);
				rateline_cnt++;
				break;
			case 5:
				if (Record05Position == 0) Record05Position = RecordPosition;
				nRecordLength = 366;
				strcpy((char*)&Parm.minchgs, buffer);
				b05_len = strlen(buffer);
				break;
			case 6:
				if (DiscountPosition == 0) DiscountPosition = RecordPosition;
				nRecordLength = 54;
				strcpy((char*)&Parm.wbdisc_in, buffer);
				break;
			case 7:
				if (IndDiscountPosition == 0) IndDiscountPosition = RecordPosition;
				nRecordLength = 54;
				strcpy((char*)&Parm.indir_in, buffer);
				break;
			default:
				printf("Was not expecting an %-2.2s record.\r\n", buffer);
				printf("Processing ceases.\r\n");
				exit(0);
		}
    }

    if (no_items >= LNE_DIM && czarstat == 0)
    {
		czarstat = 0x383;
    }

    if (no_items < DetailCount && czarstat == 0)
    {
		czarstat = 0x380;
    }

    if (no_items > DetailCount && czarstat == 0)
    {
		czarstat = 0x391;
    }

    return ReturnCode;
}


void PutBill(FILE* fp)
{
	int     x, y, z, clsidx;
	int     lne_idx;
	char    crlf[2];
	char	test_cls[4];
    char    buffer[400];
	long    RecordPosition;


#if defined(UNIX) || defined(WIN32)
#define INTFMT1 "%1.1hd"
#define INTFMT2 "%02.02hd"
#else
#define INTFMT1 "%1.1d"
#define INTFMT2 "%02.02d"
#endif

    fseek(fp, CurrentPosition, SEEK_SET);
    memset(Parm.header.crlf, '\0', 2);
	*(Parm.header.crlf-1) = '*';
    memcpy(buffer, &Parm.header, sizeof(HEADER));
    buffer[sizeof(HEADER)] = '\0';
    if (fprintf(fp, "%-s\r\n", &Parm.header) == 0)
		io_error(file_name);

    if (czarstat == 0x9999 || czarstat == 0x0383)  // don't write dtl, just skip
		return; 				    

    fseek(fp, DetailPosition, SEEK_SET);
    for (lne_idx = 0; lne_idx < DetailCount; lne_idx++)
    {
		memset(buffer, '*', sizeof(DETAIL));
		Parm.detail_arr[lne_idx].crlf[0] = '\0';
		Parm.detail_arr[lne_idx].crlf[1] = '\0';
		*Parm.detail_arr[lne_idx].fill = '*';
		memcpy(buffer, &Parm.detail_arr[lne_idx], sizeof(DETAIL));
		fprintf(fp, "%-s\r\n", buffer);
    }
    fflush(fp);

    crlf[0]= 0x0d; crlf[1]= 0x0a;
    if (b03_len > 0)
    {
		memset(&erte_rec,' ',sizeof(ERTE_REC));
		memcpy(erte_rec.rc, "03", 2);
		sprintf(erte_rec.class_cnt,INTFMT2, Parm.ert_subs.class_cnt);
		for (x= 0; x <	CLSMAX; x++)
			sprintf(erte_rec.class_tab[x],"%-3.3s", Parm.ert_subs.class_tab[x]);
		erte_rec.fill1[0] = '|';
		sprintf(erte_rec.wgt_cnt, INTFMT2, Parm.ert_subs.wgt_cnt);
		for (x= 0; x <	CLSMAX; x++)
			sprintf(erte_rec.wgt_tab[x],"%5.5ld", Parm.ert_subs.wgt_tab[x]);
		erte_rec.endflag[0] = '*';
		memcpy(buffer, &erte_rec, sizeof(ERTE_REC));
		buffer[sizeof(ERTE_REC)] = '\0';
		fseek(fp, Record03Position, SEEK_SET);
		fprintf(fp, "%-s\r\n", buffer);
		fflush(fp);
   }

    if (b04_len > 0)
    {
		memset((char*)&rate_line, '\0', sizeof(RATE_LINE));
		fseek(fp, Record04Position, SEEK_SET);
		for (x = 0; x < rateline_cnt; x++)
		{
			RecordPosition = ftell(fp);
			fgets(buffer, sizeof(buffer), fp);
			buffer[strlen(buffer) - 2] = '\0';
			strcpy((char*)&rate_line, buffer);
			memset(test_cls, 0, 4);
			memcpy(test_cls, rate_line.cls, 3);
			if (test_cls[2] == ' ')
				test_cls[2] = '\0';
			y=0;		    // find 04 class in class_tab
			while(y < Parm.ert_subs.class_cnt && memicmp(test_cls, Parm.ert_subs.class_tab[y],3) != 0)
				y++;
			if (y == Parm.ert_subs.class_cnt)    // class not found
				rate_line.val_cls[0] = 'N';
			else
			{
/*
				rate_line.val_cls[0] = 'Y';
				clsidx= (y * Parm.ert_subs.wgt_cnt) + 4;
				for (z= 0; z <	4; z++)
					sprintf(rate_line.minchg[z],"%6.6ld", Parm.expanded_rates[z]);
				rate_line.fill1[0] = '|';
				for (z = 0; z <	Parm.ert_subs.wgt_cnt; z++)
					sprintf(rate_line.rate[z],"%6.6ld", Parm.expanded_rates[clsidx+z]);
*/
				rate_line.val_cls[0] = 'Y';
				clsidx= (y * Parm.ert_subs.wgt_cnt) + 4;
				for (z= 0; z <	4; z++)
					sprintf(rate_line.minchg[z],"%6.6ld", Parm.expanded_rates[z]);
				rate_line.fill1[0] = '|';
				for (z = 0; z <	Parm.ert_subs.wgt_cnt; z++)
					sprintf(rate_line.rate[z],"%6.6ld", Parm.expanded_rates[clsidx+z]);
				for (z = Parm.ert_subs.wgt_cnt; z < 9; z++)
					sprintf(rate_line.rate[z],"%-6s", "000000");
			}
			rate_line.endflag[0] = '*';
			fseek(fp, RecordPosition, SEEK_SET);
			memcpy(buffer, &rate_line, sizeof(RATE_LINE));
			buffer[sizeof(RATE_LINE)] = '\0';
			fprintf(fp, "%-s\r\n", buffer);
			fflush(fp);
		}
    }

    if (b05_len > 0)
    {
		memset(mc_rec.rc,' ',sizeof(MINCHG_REC));
		memcpy(mc_rec.rc, "05", 2);
		sprintf(mc_rec.mc_type,INTFMT1, Parm.minchgs.mc_type);

		y = 0;

		for (x= 0; x <	Parm.minchgs.mc_cnt; x++)
		{
			if (Parm.minchgs.mc_chg[x] > 0L)
			{
				sprintf(mc_rec.mc[y].delim,"|%1c%5.5ld%5.5ld",
				Parm.minchgs.mc_ID[x], Parm.minchgs.mc_low_wgt[x], Parm.minchgs.mc_chg[x]);
				y++;
			}
		}
		sprintf(mc_rec.mc_cnt, INTFMT2, y);    // minchgs.mc_cnt);
		for (x= 0; x <	MCMAX; x++)
			mc_rec.mc[x].delim[0]= '|';
		mc_rec.endflag[0] = '*';
		memcpy(buffer, &mc_rec, sizeof(MINCHG_REC));
		buffer[sizeof(MINCHG_REC)] = '\0';
		fseek(fp, Record05Position, SEEK_SET);
		fprintf(fp, "%-s\r\n", buffer);
		fflush(fp);
    }

    return;
}



void cnvrt_err(char *err_no)
{

   if (strcmp(err_no,"   7")==0)
      strcpy(err_no," 119");
   if (strcmp(err_no,"  19")==0)
      strcpy(err_no," 400");
}

INT write_rec_err()
{
   char num_val[11];

   memset(num_val, 0, sizeof(num_val));
   sprintf(num_val, "%04x", czarstat);
   cnvrt_err(num_val);
   sscanf(num_val, "%4x", &czarstat);
   return(0);
}

