/*    VERSIONS.H   */
/*
VERSION AND MODIFICATION STRUCTURE
*/
#define VER_DEF
typedef struct {
    char  czar_prog_nm[20];
    char  czar_ver_number[3];
    char  czar_mod_number[5];
    char  bczar_prog_nm[20];
    char  bczar_ver_number[3];
    char  bczar_mod_number[5];
    char  fxlib_prog_nm[20];
    char  fxlib_ver_number[3];
    char  fxlib_mod_number[5];
    char  datatype[15];
    char  compiler[6];
} VERSIONS;
