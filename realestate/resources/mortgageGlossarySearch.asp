<%@LANGUAGE="VBSCRIPT" CODEPAGE="1252"%>
<!--#include file="Connections/realestate.asp" -->
<%
Dim results__letter
results__letter = ""
If (Request.Form("keywords")     <> "") Then 
  results__letter = Request.Form("keywords")    
End If
%>
<%
Dim Recordset1
Dim Recordset1_numRows

Set Recordset1 = Server.CreateObject("ADODB.Recordset")
Recordset1.ActiveConnection = MM_realestate_STRING
Recordset1.Source = "SELECT DISTINCT * FROM MortgageGlossary WHERE glossaryTerm LIKE '%" + Replace(results__letter, "'", "''") + "%' OR glossaryDefinition LIKE '%" + Replace(results__letter, "'", "''") + "%' ORDER BY glossaryTerm ASC"
Recordset1.CursorType = 0
Recordset1.CursorLocation = 2
Recordset1.LockType = 1
Recordset1.Open()

Recordset1_numRows = 0
%>
<%
Dim Repeat1__numRows
Dim Repeat1__index

Repeat1__numRows = -1
Repeat1__index = 0
Recordset1_numRows = Recordset1_numRows + Repeat1__numRows
%>
<%
' Parameters:
' strText 	- string to search in
' strFind	- string to look for
' strBefore	- string to insert before the strFind
' strAfter 	- string to insert after the strFind
'
' Example: 
' This will make all the instances of the word "the" bold
'
' Response.Write Highlight(strSomeText, "the", "<b>", "</b>")
'
Function Highlight(strText, strFind, strBefore, strAfter)
	Dim nPos
	Dim nLen
	Dim nLenAll
	
	nLen = Len(strFind)
	nLenAll = nLen + Len(strBefore) + Len(strAfter) + 1

	Highlight = strText

	If nLen > 0 And Len(Highlight) > 0 Then
		nPos = InStr(1, Highlight, strFind, 1)
		Do While nPos > 0
			Highlight = Left(Highlight, nPos - 1) & _
				strBefore & Mid(Highlight, nPos, nLen) & strAfter & _
				Mid(Highlight, nPos + nLen)

			nPos = InStr(nPos + nLenAll, Highlight, strFind, 1)
		Loop
	End If
End Function
%>
<html>
<head>
<title>Mortgage Glossary</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="styles.css" rel="stylesheet" type="text/css">

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function actFloatLayer(Margin, slideTime, layerName, Type, browser)
{  
   //===== initial the properties ===== begin =====
   
   switch(browser)
   {
      case (1):
                  find = (Type < 3) ? document.body.clientHeight : document.body.clientWidth;
                  current = (Type < 3) ? eval(layerName + ".style.pixelTop") : eval(layerName + ".style.pixelLeft");
                  scrollAt = (Type < 3) ? document.body.scrollTop : document.body.scrollLeft;
                  break;

      case (2): 
                  find = (Type < 3) ? window.innerHeight : window.innerWidth;
                  current = (Type < 3) ? eval("document." + layerName + ".top") : eval("document." + layerName + ".left");
                  scrollAt = (Type < 3) ? window.pageYOffset : window.pageXOffset;
                  break;

      case (3): 
                  find = (Type < 3) ? window.innerHeight : window.innerWidth;
                  current = (Type < 3) ? parseInt(eval("document.getElementById('" + layerName + "').style.top")) : parseInt(eval("document.getElementById('" + layerName + "').style.left")); 
                  scrollAt = (Type < 3) ? window.scrollY : window.scrollX;
                  break;
   }

   //===== initial the properties ===== end =====
 

   //========== Main Trigger ========== begin =====
   
   switch(Type)
   {
      case (1): case (3): //Type : top and left
         newTarget = scrollAt + Margin;
         break;
      case (2): case (4): //Type : bottom and right
         newTarget = scrollAt + find - Margin;
         break;
   }
   
   if ( current != newTarget ) 
   {        
      if (newTarget !=  this.target ) 
      { 
         //========== Start Float ========== begin =====

         target = newTarget;

         now = new Date();
         Mul = target - current;
         Wave = Math.PI / ( 2 * slideTime );
         Pass = now.getTime();

         if (Math.abs(Mul) > find) 
         { 
            
            Dis = (Mul > 0) ? target - find : target + find ;
            Mul = (Mul > 0) ? find : -find ;
         }
   
         else 	 
            Dis = current 

         //========== Start Float ========== end =====
      } 
      
      //========== Animator ========== begin =====

      now = new Date();
      newPosition = Mul * Math.sin( Wave * ( now.getTime() - Pass ) ) + Dis;

      newPosition = Math.round(newPosition);

      if (( Mul > 0 && newPosition > current ) || ( Mul < 0 && newPosition < current )) 
      { 
         switch(browser)
         {
            case (1):
                        (Type < 3) ? eval(layerName + ".style.pixelTop = newPosition;") : eval(layerName + ".style.pixelLeft = newPosition;");
                        break;
            case (2):
                        (Type < 3) ? eval("document." + layerName + ".top = newPosition;") : eval("document." + layerName + ".left = newPosition;");
                        break;
            case (3):
                        (Type < 3) ? eval("document.getElementById('" + layerName + "').style.top = newPosition + 'px';") : eval("document.getElementById('" + layerName + "').style.left = newPosition + 'px';");
                        break;
         }         
      }       

      //========== Animator ========== end =====
   } 

   //========== Main Trigger ========== end =====
}

function startFloatLayer(layerName, x, Margin, slideTime, Type) 
{
   //===== verify the Type ===== begin =====
   var browser;

   if (document.all)
      browser = 1;  //ie4

   if (document.layers)
      browser = 2;  //ns4

   if (!document.all && document.getElementById)
      browser = 3 ; //ns6
   
   setInterval("actFloatLayer(" + Margin + ", " + slideTime + ", '" + layerName + "', " + Type + ", " + browser + ")", 10);
}
//-->
</script>
<style type="text/css">
<!--
.style1 {font-size: x-small}
-->
</style>
</head>

<body onLoad="startFloatLayer('Layer1', '', 100, 1200, 1)">
<table width="710" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td height="80" align="left" valign="top"><img src="images/mortgageTop.gif" width="710" height="80"></td>
  </tr>
  <tr> 
    <td align="left" valign="top" background="images/guideBG.gif"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr valign="top"> 
          <td width="151">
<div align="center"> 
              <div id="Layer1" style="position:absolute; width:148px; height:338px; z-index:1; left: 3; top: 97px;">
                <div align="right">
                  <p align="center">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="10">&nbsp;</td>
                      <td>
                        <form name="form1" method="post" action="mortgageGlossarySearch.asp">
                          <table border="0" align="center" cellpadding="3" cellspacing="0">
                            <tr>
                              <td><div align="left"><img src="images/search.gif" width="110" height="16"></div></td>
                            </tr>
                            <tr>
                              <td><div align="left">
                                  <input name="keywords" type="text" size="15">
                              </div></td>
                            </tr>
                            <tr>
                              <td><div align="right">
                                  <input type="submit" name="Submit" value="Search">
                              </div></td>
                            </tr>
                          </table>
                      </form></td>
                      <td width="10">&nbsp;</td>
                    </tr>
                  </table>
                  <div align="center"><!--#include file="mortgageLinks.html"-->
                </div>
              </div>
              <p align="center">&nbsp; </p>
              <p align="right">&nbsp; </p>
              <p>&nbsp;</p>
            </div></td>
          <td> <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="20">&nbsp;</td>
                <td> <table border="0" align="center" cellpadding="2" cellspacing="4">
                    <tr align="center" valign="top"> 
                      <td> <div align="center"></div></td>
                      <td>&nbsp;</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=a">A</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=b">B</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=c">C</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=d">D</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=e">E</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=f">F</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=g">G</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=h">H</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=i">I</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=j">J</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=k">K</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=l">L</a></font></div></td>
                      <td>&nbsp;</td>
                      <td> <div align="center"></div></td>
                    </tr>
                    <tr align="center" valign="top"> 
                      <td height="30"> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=m">M</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=n">N</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=o">O</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=p">P</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=q">Q</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=r">R</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=s">S</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=t">T</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=u">U</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=v">V</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=w">W</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=x">X</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=y">Y</a></font></div></td>
                      <td>|</td>
                      <td> <div align="center"><font size="4" face="Georgia, Times New Roman, Times, serif"><a href="mortgageGlossary.asp?letter=z">Z</a></font></div></td>
                    </tr>
                  </table>
                  <p><font color="#000000">                    <font color="#CC0000" size="2"><strong><img src="images/searchResults.gif" width="117" height="15"></strong></font></font></p>
                  <table border="0" cellpadding="2" cellspacing="0">
				  <% if Recordset1.EOF then response.Write("<font size='2'><strong>Sorry!</strong> There are no terms or definitions that contain your keywords. Please try again!</font><br><br>") end if %>
                    <% While ((Repeat1__numRows <> 0) AND (NOT Recordset1.EOF)) %>
                    <tr> 
                      <td>
					  
					    <div align="justify"><font size="2"><strong>
					      <%= Response.Write(Highlight(Recordset1.Fields.Item("glossaryTerm").Value,Request.Form("keywords"),"<font color=#cc0000>","</font>")) %>
					      </strong><br>
					      <%= Response.Write(Highlight(Recordset1.Fields.Item("glossaryDefinition").Value,Request.Form("keywords"),"<font color=#cc0000>","</font>")) %>
					      </font>
						  
					    </div></td>
                    </tr>
                    <tr>
                      <td height="20">&nbsp;</td>
                    </tr>
                    <% 
  Repeat1__index=Repeat1__index+1
  Repeat1__numRows=Repeat1__numRows-1
  Recordset1.MoveNext()
Wend
%>
                  </table>
                  <p align="center"><span class="style1"><font size="2"><a href="index.html"><b>&lt;&lt; Go
                            Back to the Real Estate Resource Center Home Page</b></a></font><font size="1"><br>
Content by <font size="1">4
Site USA, LLC</font>
                          </font></span></p></td>
                <td width="30">&nbsp;</td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td height="20" align="left" valign="top" background="images/guideBG.gif">&nbsp;</td>
  </tr>
</table>
<noscript>
<h1>4 Site USA delivers high quality professional <a href="http://www.4siterealestate.com" title="Real Estate WebSites and Designs by 4 Site">Real Estate Websites & designs</a>, for your competitive advantage. 4 Site provides top quality, professional <a href="http://www.4siteusa.com" title="4 Site; web design, web programming and web hosting services" target="_blank">web designs and web hosting services</a>.</h1>
</noscript>
</body>
</html>
<%
Recordset1.Close()
Set Recordset1 = Nothing
%>
