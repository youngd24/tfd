
.PHONY : all clean docs test

all : docs

clean :
	@echo "Making clean"
	@rm *.x~~

test : 
	perl t.pl

docs :
	@echo "Creating HTML documentation from source POD"
	pod2html --title Digiship::Debug \
                 --htmlroot=/docs \
                 --htmldir /digiship/docs/Digiship \
                 --outfile ../../docs/Digiship/Debug.html \
                 --backlink Top \
                 --css pod.css \
                 --verbose \
                 --flush \
                 --index \
                 --infile Debug.pod

	pod2html --title Digiship::Error \
                 --htmlroot=/docs \
                 --htmldir /digiship/docs/Digiship \
                 --outfile ../../docs/Digiship/Error.html \
                 --backlink Top \
                 --css pod.css \
                 --verbose \
                 --flush \
                 --index \
                 --infile Error.pod
	@echo "Done"
