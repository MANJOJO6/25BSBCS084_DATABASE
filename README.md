project title: homeland hospital.
name: Engwedu Joseph.
registration number: 25BSCS084.
A hospital uses database. My name is Joseph and my project (website) is going to be Homeland Hospital database management system for Patient Appointment & Communication Portal.
In my website, patients can book appointments with doctors, view their visit history, and communicate directly with their healthcare providers that is to say doctors can manage their schedules with patients, review patient requests, and chat with patients .
Technologies used: PHP

steps to run the project
Step 1 
After XAMPP is running, the person needs to locate the XAMPP installation folder on their computer. Inside that folder, there is a subfolder called htdocs — this is where all websites are stored. They should create a new folder inside htdocs and name it homeland_hospital. Then they copy all your project files into that folder. The folder should contain all the PHP files such as index.php, login.php, dashboard.php, config.php, and all the others, plus the style.css file.

Step 2 
Next, they need to create the database. They should open their web browser and go to http://localhost/phpmyadmin. This opens phpMyAdmin, which is a tool for managing databases. Once inside, they click on the "Import" tab at the top. Then they click "Choose File" and select the DATABASE.sql file from your project folder. After selecting the file, they scroll down and click "Go." phpMyAdmin will automatically create the homeland_hospital database, create all the tables (patients, doctors, appointments), and insert all the sample data including the test doctors and patients. They should see a green success message when it is done.

Step 3 
Your project already has a file called config.php that connects to the database. The default settings in this file use localhost as the host, root as the username, an empty password, and homeland_hospital as the database name. This matches the default XAMPP setup exactly, so in most cases nothing needs to be changed. However, if the person has set a password for their MySQL root account, they will need to open config.php and type that password into the $pass variable.

Step 4 
Now the project is ready to run. They open their browser and go to http://localhost/homeland/. The Homeland Hospital homepage should appear with options to log in as a patient or as a doctor.

Step 5 
To test as a patient, they click "Patient Login" and use any of the sample patient accounts. For example, they can type alice.johnson@example.com as the email and patient123 as the password. From the patient dashboard they can browse doctors, book appointments, view appointment history, and access the chat. To test as a doctor, they go back to the homepage, click "Doctor Login," and use any doctor account such as sarah.mitchell@homeland.com with the password doctor123. From the doctor dashboard they can view their schedule, manage patient appointments, and use the chat feature.
https://github.com/MANJOJO6/25BSBCS084_DATABASE.git
