#define HDR_LEN   264
#ifndef DTL_LEN
#define DTL_LEN  29
#endif

#define CLSMAX     20
#define MCMAX      30

  typedef struct {			 // '01' Header record layout 
    CHAR  record_code[2];      //   1 
    CHAR  error_status[4];     //     
    CHAR  program_mode[4];
    CHAR  tariff_name[8];
    CHAR  intra_state[1];
    CHAR  shipment_date[10];
    CHAR  orgzip[6];
    CHAR  orgcity_state[19];
    CHAR  dstzip[6];
    CHAR  dstcity_state[19];
    CHAR  detail_lines[2];
    CHAR  tariff_authority[8];
    CHAR  rbno[11];
    CHAR  actual_weight[6];
    CHAR  billed_weight[6];
    CHAR  min_charge[7];
    CHAR  dfc_rate[7];
    CHAR  dfc_weight[6];
    CHAR  dfc_charges[9];
    CHAR  total_charges[9];
    CHAR  effective_date[10];
    CHAR  pro_number[16];
    CHAR  single_shipment[1];  // 3.50 clo added code 1=SS apply YFS logic 
    CHAR  tl_min_chg[8];
    CHAR  tl_max_chg[8];
    CHAR  use_dscnts[1];
    CHAR  return_pointnms[1];  // 195 
    CHAR  org_rout;	       // 196 
    CHAR  dst_rout;	       // 197 
    CHAR  carrier_charges[9];  // 198 
    CHAR  variance[6];	       // 207 
    CHAR  routing_override[1]; // 213 3.50 9/28/97 clo O,D or B 
    CHAR  discount_type[1];    // 214 3.50 9/28/97 clo R=rates C=charges defaults to charges 
    CHAR  ss_add[5];	       // 215 3.50 9/28/97 clo additive to SS charges 
    CHAR  dscamt_LH[9];        // 220 3.50 1/16/98 clo discount amount for Line haul 
    CHAR  dscamt_MC[7];        // 229 3.50 1/16/98 clo discount amount for MC 
    CHAR  rtadjfac[5];	       // 236 3.50 1/16/98 clo rate adjustment factor 
    CHAR  user_filler[22];     // 241 224=Org point, 243= Dest Point
    CHAR  crlf[2];
  } HEADER;				// 264 bytes 

  typedef struct {		    // 'D%' Discounts record layout 
    CHAR  record_code[2];
    CHAR  dscnt[10][5]; 	    // _99v99 
    CHAR  crlf[2];
  } WBDISC_IN;		       // 54 bytes 

  typedef struct {			// '02' Detail record layout 
    CHAR  record_code[2];
    CHAR  cls[3];
    CHAR  wgt[6];
    CHAR  rte[7];
    CHAR  chg[9];
    CHAR  crlf[2];
    CHAR  fill[1];
  } DETAIL;		       // 30 bytes 

   typedef struct {		   // '03' Class/Weight record layout 
      CHAR  rc[2];
      CHAR  class_cnt[2];
      CHAR  class_tab[CLSMAX][3]; // 'xxx' ex. '100' but '77_' 
      CHAR  fill1[1];		  // any char, to delim end of cls 
      CHAR  wgt_cnt[2];
      CHAR  wgt_tab[CLSMAX][5];   // low wgt '99999' 
      CHAR  endflag[1]; 	  // any char, to delim end of rec 
   } ERTE_REC;		       // 168 bytes 

   typedef struct {		// '04' Class Rate Line record layout 
      CHAR  rc[2];
      CHAR  cls[3];	      // 'xxx' ex. '100' but '77_' 
      CHAR  val_cls[1];     // 'Y'=Valid, 'N'=Invalid, ' '=Not processed 
      CHAR  minchg[4][6];     // 9999v99 
      CHAR  fill1[1];	      // any char, to delim end of m/c 
      CHAR  rate[9][6];       // 9999v99 
      CHAR  endflag[1];       // any char, to delim end of rec 
   } RATE_LINE; 	       // 86 bytes 

  typedef struct {	      // '05' Minimum Charge record layout 
      CHAR  rc[2];
      CHAR  mc_type[1];
      CHAR  mc_cnt[2];
      struct {
	 CHAR  delim[1];      // '|'  (or any)     
	 CHAR  mc_ID[1];      // 0= SS, 1= Multi   
	 CHAR  mc_low_wgt[5]; // 0 - 40,000	   
	 CHAR  mc_chg[5];     // in cents	   
      } mc[MCMAX];
      CHAR  endflag[1];       // any char, to delim end of rec 
  } MINCHG_REC; 	       // 366 bytes 

  typedef struct {
      CHAR	class_tab[20][6];
      LONG	wgt_tab[20];
      INT	class_cnt;
      INT	wgt_cnt;
 } ERTESUBS;		       // 204 bytes 

  typedef struct {
      CHAR  mc_ID[MCMAX];   // '0'= SS, '1'= Multi  
      CHAR  fill1[2];
      LONG  mc_low_wgt[MCMAX];
      LONG  mc_chg[MCMAX];
      INT   mc_type;
      INT   mc_cnt;
  } MINCHGS;		       // 276 bytes 

//*
  typedef struct {	// error 124 City List record layout  
      CHAR State[2],	// State abbreviation  
	   City[17],	// City name	       
	   NullByte;	// pad to even # bytes 
  } CITYLIST;		       // 20 bytes 

  typedef struct {	// error 126 Tariff List record layout	
      CHAR Tariff[6],	// Tariff name	       
	   Section[2],	// Section	       
	   Month[2],	// Effective month     
	   Day[2],	// Effective day       
	   Year[2],	// Effective year      
	   Percentage[6],  // Increase over base rate 
	   IA[2],	// 'IA' if independent announcment 
	   NullByte[2]; // pad to even # bytes 
  } TARIFFLIST; 	       // 24 bytes 

  typedef struct {
    CHAR  record_code[2]; //  D% or I% 
    FLOAT dscnt[10];	  //  (99.99%) 
    INT   wgtbrk_used;	  //  0==MC    
  } WBDISC;
