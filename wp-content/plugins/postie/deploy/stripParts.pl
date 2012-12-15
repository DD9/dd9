#!/usr/bin/perl -w
use strict;
my $string = do {local ( $/ ); <> }; 
$string=~s/.*(<h3>Installation)/$1/s; 
$string=~s/(.*)<h3>Changelog.*(<h3>.*)/$1$2/s; 
$string=~s/(.*)<h3>Screenshots.*(<h3>.*)/$1$2/s; 
if ($string=~s/(<h3>Frequently Asked Questions<\/h3>.*?)<hr \/>//s) {
  my $faq=$1;
  my $questions;
  my $faqID=0;
  while ($faq=~s/<h4>(.*)<\/h4>/<h4 id='answer-$faqID'>$1<\/h4>/) {
    $questions.="<li id='question-$faqID'><a href='#answer-$faqID'>$1</a></li>\n";
    $faqID++;
  }
  $faq=$questions.$faq;
  my $faqFile='faq.html';
  open(FAQ,">$faqFile");
  print FAQ $faq;
  close(FAQ);
}
$string=~s/<h2 id=\'re-edit'.*//s; 
print $string;
