#!/bin/sh
#

RATECLIENT="./RateClient"

# dies with a message
croak ()
{
	$MSG=$1
	echo $MSG
	exit 1
}

# Prints the usage
printUsage ()
{
	echo "Usage: rate [environment]"
	echo "  where [environment] is:"
	echo "       local - 127.0.0.1"
	echo "       dev   - 192.168.4.20"
	echo "       prod  - 216.80.68.206"
}

# Prints the version
printVersion ()
{
	echo "$Id: rate.sh,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $"
}

# Rate from the local machine
doLocal ()
{
	${RATECLIENT} --carrier=RDWY --srczip=60601 --dstzip=45345 --server=127.0.0.1 --weight=15000 --class=55 
	if [ $? != 0 ]
	then
		croak "Rating error"
	fi
}

# Rates from the development server
doDev ()
{
	${RATECLIENT} --carrier=RDWY --srczip=60601 --dstzip=45345 --server=192.168.4.20 --weight=15000 --class=55 
	if [ $? != 0 ]
	then
		croak "Rating error"
	fi

}

# Rates from the production machine
doProd () 
{
	${RATECLIENT} --carrier=RDWY --srczip=60601 --dstzip=45345 --server=216.80.68.206 --weight=15000 --class=55 
	if [ $? != 0 ]
	then
		croak "Rating error"
	fi
}


# Main program, work based off the command line
case $1 in
     "--version") printVersion
                  exit
               ;;
        "--help") printUsage
                  exit
               ;;
         "local") doLocal
                  exit
               ;;
           "dev") doDev
                  exit
               ;;
          "prod") doProd
                  exit
               ;;
               *) printUsage
                  exit
               ;;
esac
