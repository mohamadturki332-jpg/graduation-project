' Launches the helpdesk email-to-ticket listener with no visible window.
' Registered in Windows Task Scheduler to start at logon so incoming emails
' become tickets automatically while the machine is on.
Set sh = CreateObject("WScript.Shell")
sh.CurrentDirectory = "C:\Users\Moham\projects\helpdesk"
' 0 = hidden window, False = don't wait (the listener runs forever)
sh.Run "C:\xampp\php\php.exe artisan tickets:fetch-emails --interval=5", 0, False
