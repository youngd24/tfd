#
# Rate Server Commands Module
# 
package RSCommands;


# Process the HELP command
sub cmdHelp {
	my $client = shift;

}


# Process the QUIT command
sub cmdQuit {
	my $client = shift;

}


# Process the VERSION command
sub cmdVersion {
	my $client = shift;
}


# Process the BASEPRICE command
sub cmdBasePrice {
	my $client = shift;

}


# Process the TRANSTIME command
sub cmdTransTime {
	my $client = shift;

}


# Process the SAVESTATS command
sub cmdSaveStats {
	my $client = shift;

}



# Process the INFO command
sub cmdInfo {
	my $client = shift;

}


# Process the SHOWCONFIG command
sub cmdShowConfig {
	my $client = shift;

}










# Switch format
#
# SWITCH: {
#	/^Item1/ 	&& do 	{
#							$val = 1
#							last SWITCH;
#						};
#	/^Item2/	&& do 	{
#							$var = 2;
#							last SWITCH;
#						};


1;
