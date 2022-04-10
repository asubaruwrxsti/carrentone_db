# carrentone_db
Uses the same methodology as the previous project, but every edit is maintained in a minimal database table, not a file.
Needs some performance tweaking due to the fact that it generates new html indexes every time the user commits an update.
It updates the properties of the entry in the database, but uses them to generate an html file in the right directory
and not pull the dynamic data directly from the database.
