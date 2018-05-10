# =============================================================================
#
# PASSLIB.PM
#
# Digiship EDI/FTP Server Password Library
#
# $Id: Passlib.pm,v 1.1.1.1 2002/07/13 04:30:35 youngd Exp $
#
# Contents Copyright(c) 2000, 2001 Digiship Corp.
#
# Darren Young
# youngd@digiship.com
#
# =============================================================================

# Our package name
package Passlib;

# Modules that we need
# use warnings;

BEGIN {

    print "Entering " . __PACKAGE__ . "\n" if $main::dbg;
    use Exporter   ();
    our ( $VERSION, @ISA, @EXPORT, @EXPORT_OK, %EXPORT_TAGS );

    $VERSION   = '1.00';

    @ISA       = qw(Exporter);

    # Default exported functions
    @EXPORT    = qw(genPass askPass addUser checkPass getPassFromFile isUser);

    # Functions exported on request
    @EXPORT_OK = ();

}

# ---------------------------------------------------------------------------
# NAME        : genPass
# DESCRIPTION : Generate a crypted password based on a plaintext one
# NEEDS       : String Plaintext Password
# RETURNS     : String Crypted Password
# NOTES       : None
# ---------------------------------------------------------------------------
#
sub genPass {
	my ( $plaintext ) = @_;
    my $salt;
    my $crypted;
    
    print "Entering genPass($plaintext)\n" if $main::dbg;
	
	# Make sure they give us a password
	if ( $plaintext eq "" ) {
	   print "genPass usage: genPass(password) [password not supplied]\n";
	   return(0);
	}

	$salt = join ('', ('.', '/', 0..9, 'A'..'Z', 'a'..'z')[rand 64, rand 64]);
	$crypted = crypt($plaintext,$salt);
	
	return($crypted);
}


# ---------------------------------------------------------------------------
# NAME        : askPass
# DESCRIPTION : Prompts the user for a password (twice)
# NEEDS       : None
# RETURNS     : String Password
# NOTES       : None
# ---------------------------------------------------------------------------
# 
sub askPass {
    my ($pass1, $pass2);
    print "Entering askPass\n" if $main::dbg;
    
    print "Password: ";
    $pass1 = <STDIN>;
    chop($pass1);
    print "Password again: ";
    $pass2 = <STDIN>;
    chop($pass2);
    
    if ( $pass1 eq $pass2 ) {
        return($pass2);
    } else {
        print "Passwords don't match\n";
        return(0)
    }
}


# ---------------------------------------------------------------------------
# NAME        : addUser
# DESCRIPTION : Adds a user to a EDI/FTP users file
# NEEDS       : String Filename, String Username, String Crypted Password
# RETURNS     : Status Code
# NOTES       : None
# ---------------------------------------------------------------------------
# 
sub addUser {
    my ( $file, $user, $password ) = @_;
    my $crypted;
    
    print "Entering addUser($file, $user, $password)\n" if $main::dbg;
    
    # Make sure they give us the file to go at
	if ( $file eq "" ) {
	   print "addUser usage: addUser(file, user, password) [file not supplied]\n";
	   return(0);
	}
    
    # Make sure they give us the user
	if ( $user eq "" ) {
	   print "addUser usage: addUser(file, user, password) [user not supplied]\n";
	   return(0);
	}

    # Make sure they give us the password
	if ( $password eq "" ) {
	   print "addUser usage: addUser(file, user, password) [password not supplied]\n";
	   return(0);
	}

    open(PASSFILE, ">>$file") || die "Unable to open file $file ($!)\n";
    
    # Encrypt the password, salt is generated in there
    $crypted = genPass($password) || die "Unable to generate encrypted password\n";

    print "Printing " . $user . ":" . $crypted . "to users file\n" if $main::dbg;
    print PASSFILE $user . ":" . $crypted . "\n";
        
    close(PASSFILE);
    return(1);
}


# ---------------------------------------------------------------------------
# NAME        : checkPass
# DESCRIPTION : Validates a users encrypted password
# NEEDS       : String Crypted Password, String Plaintext Password
# RETURNS     : Status Code
# NOTES       : None
# ---------------------------------------------------------------------------
# 
sub checkPass {
	my ( $crypted, $plain ) = @_;
    my $usedSalt;
    my $usedCrypt;
    my $newCrypt;
    
	print "Entering checkPass($crypted, $plain)\n" if $main::dbg;
	
	# Make sure they give us a password
	if ( $crypted eq "" ) {
	   print "genPass usage: genPass(password) [password not supplied]\n";
	   return(0);
	}	

	# Get the salt used to crypt the password
	$usedSalt = substr($crypted,0,2);
	print "Old salt -> $usedSalt\n" if $main::dbg;
	# Get what is the crypted password
	$usedCrypt = substr($crypted,2);
	print "Old password -> $usedCrypt\n" if $main::dbg;
	
	# Crypt the given plain text with the old salt
	$newCrypt = crypt($plain, $usedSalt);
    print "Newly crypted password -> $newCrypt\n" if $main::dbg;
	
	if ( $newCrypt ne $crypted ) {
        # New crypted password is not the same as the old one
        # The one we were asked to validate is bad
        print "They don't match\n" if $main::dbg;
        return(0);
	} else {
        # New crypted password is the same as the old
        # The one we were asked to validate is good
        print "They seem to match\n" if $main::dbg;
        return(1);
	}		
}


# ---------------------------------------------------------------------------
# NAME        : getPassFromFile
# DESCRIPTION : Retrieve a user's password from a file
# NEEDS       : String Username, String Filename
# RETURNS     : String Crypted Password
# NOTES       : None
# ---------------------------------------------------------------------------
# 
sub getPassFromFile {
	my ( $file, $username ) = @_;

    print "Entering getPassFromFile($file, $username)\n" if $main::dbg;
    	
	# Make sure they give us the file to go at
	if ( $file eq "" ) {
	   print "getPassFromFile usage: getPassFromFile(file, username) [file not supplied]\n";
	   return(0);
	}
	
	# Make sure they give us the username to look for
	if ( $username eq "" ) {
	   print "getPassFromFile usage: getPassFromFile(file, username) [username not supplied]\n";
	   return(0);
	}
	
	# Make sure the file exists
	if ( ! -f $file ) {
	   print "getPassFromFile: File $file doesn't exist\n";
	   return(0);
	}
	
	open(PASSFILE, "<$file") || die "getPassFromFile: Unable to open file ($!)\n";
    	
	while(<PASSFILE>) {
        next if (/^#/);     # Skip comments
        next if (/^\n/);    # Skip blank lines
        chop();
        # File is in the format username:password
        my ($us, $pf) = split(':', $_);
        print "getPassFromFile: Found user $us\n" if $main::dbg;
        print "getPassFromFile: Found password $pf\n" if $main::dbg;
        if ( $us eq $username ) {
            print "getPassFromFile: Got a user match\n" if $main::dbg;
            close(PASSFILE);
            return($pf);
        }   
	}	

	close(PASSFILE) || die "getPassFRomFile: Unable to close $file ($!)\n";
    return(0);
}



# ---------------------------------------------------------------------------
# NAME        : isUser
# DESCRIPTION : Checks whether or not a username is in a file
# NEEDS       : String Filename, String Username
# RETURNS     : Status Code
# NOTES       : None
# ---------------------------------------------------------------------------
# 
sub isUser {
    my ( $file, $user ) = @_;

    print "Entering isUser($file, $user)\n" if $main::dbg;
    	
    # Make sure they give us the file to go at
	if ( $file eq "" ) {
	   print "isUser usage: isUser(file, user) [file not supplied]\n";
	   return(0);
	}
	
	# Make sure they give us the username to look for
	if ( $user eq "" ) {
	   print "isUser usage: isUser(file, user) [username not supplied]\n";
	   return(0);
	}
    
    open(PASSFILE, "<$file") || die "isUser: Unable to open file $file ($!)\n";
    
    my $hit = 0;
    
    while(<PASSFILE>) {
        next if (/^#/);     # Skip comments
        next if (/^\n/);    # Skip blank lines
        
        # Format of the file is username:password
        ( $lUser, $lPass )= split(':', $_);
        
        if ( $lUser eq $user ) {
            $hit = 1;
            close(PASSFILE) || die "isUser: Unable to close $file ($!)\n";
            return(1);
        }
    }
    close(PASSFILE);
    return(0);    
}


# ---------------------------------------------------------------------------
# NAME        : 
# DESCRIPTION : 
# NEEDS       : 
# RETURNS     : 
# NOTES       : 
# ---------------------------------------------------------------------------
# 



1;

__END__

=head1 NAME

Digiship::EDI::Passlib - Digiship EDI/FTP Password Library

=head1 SUPPORTED PLATFORMS

=over 4

=item *

Windows 95

=item *

Windows 98

=item *

Windows NT

=item *

Windows 2000

=back

=head1 SYNOPSIS

    use Digiship::EDI::Passlib;

    -or-

    use lib 'module_dir';

    use Digiship::EDI::Passlib;

=head1 DESCRIPTION

This module provides methods to maintain the Digiship EDI/FTP server's
user account database. The account database is, in basic form, is a text
file. The methods in this module provide the ability to maintain this database
file programatically.

The format of the file is similar to a UNIX password or shadow file, except that
it contains fewer fields. The only fields present in the user file are the
username and the password and are separated by a colon (:)

    username:password

The password is stored in UNIX crypted() format. This encryption method is
a one-way method, meaning there is no known method to decrypt the password.
The crypt() function was chosen over other stronger mechanisms since it is
available on most platforms, in most languages.

=head1 DEBUGGING

To receive debugging messages, simply set the variable $dbg to 1 in the 
calling script. Throughout the library there are a number of print statements
that will fire if $main::dbg is set.

Perhaps there is a more elegant way to do this, however in the interest of
time, this will do just fine. :-)

=head1 METHODS

=head2 genPass()

    Needs: String Plaintext Password

    Returns: String Crypted Password

    This method takes a plaintext password, generates a random salt, then crypts and
    returns the result to the caller.

    Usage:

        $crypted = genPass('mypassword');

=head2 askPass()

    Needs: Nothing

    Returns: String Password

    This method prompts the user for a password, then again for the password, if
    they both match, the result is returned to the caller.

    Usage:

        while( ! $password ) {
            $password = askPass();
        }

=head2 addUser()

    Needs: String Filename, String Username, String Crypted Password 

    Returns: Result Code

    This method adds a user to the named users file in the format that is currently
    in use by the server. The password is stored in crypted() format.

    Usage:

        addUser('users', 'jdoe', 'hispassword') || die "Failed to add user\n";

=head2 checkPass()

    Needs: String Crypted Password, String Plaintext Password

    Returns: Result Code
    
    This method validates that a password is correct. It does this by pulling 
    the original salt from the crypted password then crypting the tested password
    with it. If the resulting string is the same as the original, then the
    password is correct, otherwise it's not.
    
    Usage:

        if ( checkPass('RxdfGh3sD', 'password') ) {
            print "Password good\n";
        } else {
            print "Password bad\n";
        }

=head2 getPassFromFile()

    Needs: String Username, String Filename

    Returns: String Crypted Password
    
    While you could simply open a file, read it in and find the username,
    this method can be used. It simply does a lookup in the file, if the 
    requested username is there, it returns the crypted password. If the 
    user is not there, it returns 0.
    
    Usage:
    
        $userpass = getPassFromFile('jdoe', '/etc/users') || die "Unable to get pass\n";

=head2 isUser()  

    Needs: String Filename, String Username

    Returns: Result Code
    
    This method looks through the named file, and if the username is found, 
    returns 1, if not it returns 0.
    
    Usage:
    
        if ( isUser('/etc/users', 'jdoe') ) {
            print "User exists\n";
        } else {
            print "User does not exist\n";
        }

=head1 COPYRIGHT

Contents Copyright (c) 2000, 2001 Digiship Corporation

=head1 AUTHOR INFORMATION

Darren Young

youngd@digiship.com

=head1 BUGS

Er, ah, not enough testing yet.

=head1 SEE ALSO

L<Digiship::EDI>

=cut
