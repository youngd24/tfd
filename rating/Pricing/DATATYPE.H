#ifndef DATATYPE
  #if defined(UNIX) || defined(WIN32)
      /* In SCO Unix an int is 4 bytes, a short is 2  */
      #define DATATYPE "UNIX/WIN32"
      #define   BYTE      unsigned char
      #define   PACK      char
      #define   CHAR      char
      #define   DOUBLE    double
      #define   FILEHAND  short
      #define   FLOAT     float
      #define   INT       short
      #define   LONG      long
      #define   SHORT     short
      #define   UCHAR     unsigned char
      #define   UINT      unsigned short
      #define   ULONG     unsigned long
      #define   UNSIGNED  unsigned short
      #define   USHORT    unsigned short
      #define   VOID      void
      #ifndef BOOL
         #undef TRUE
         #undef FALSE
         #define   BOOL      char
         #define   TRUE      (char) 1
         #define   ERR       (char)-1
         #define   FALSE     (char) 0
      #endif
 #else
    #if defined(M_I86)
      #define DATATYPE "MI86/16-Bit"
      #define   BYTE      unsigned char
      #define   PACK      char
      #define   CHAR      char
      #define   DOUBLE    double
      #define   FILEHAND  int
      #define   FLOAT     float
      #define   INT       int
      #define   LONG      long
      #define   SHORT     short
      #define   UCHAR     unsigned char
      #define   UINT      unsigned int
      #define   ULONG     unsigned long
      #define   UNSIGNED  unsigned
      #define   USHORT    unsigned short
      #define   VOID      void
      #ifndef BOOL
         #define   BOOL      char
         #if defined(__BORLANDC__)
            #undef TRUE
            #undef FALSE
            #define TRUE          1
            #define ERR          -1
            #define FALSE         0
         #else
            #undef TRUE
            #undef FALSE
            #define TRUE  (char) -1
          #define ERR   (char) -2
            #define FALSE (char)  0
         #endif
      #endif
    #else
  /*     #define   O_BINARY  0 */ /* BORLANDC doesn't like this */
      #define DATATYPE "OTHER"
      #define   BYTE      unsigned char
      #define   PACK      char
      #define   CHAR      char
      #define   DOUBLE    double
      #define   FILEHAND  int
      #define   FLOAT     float
      #define   INT       int
      #define   LONG      int
      #define   SEEK_SET  0
      #define   SHORT     short
      #define   UCHAR     unsigned char
      #define   UINT      unsigned short
      #define   ULONG     unsigned int
      #define   UNSIGNED  unsigned short
      #define   USHORT    unsigned short   /* clo 3.50 was SHORT */
      #define   VOID      void
      #ifndef BOOL
         #undef TRUE
         #undef FALSE
         #define   BOOL      char
         #define   TRUE  (char) -1
         #define   ERR   (char) -2
         #define   FALSE (char)  0
      #endif
    #endif
  #endif
#endif //DATATYPE

