# What is this?
This is a node.js-based web development environoment used to create and deploy web content for EMS. Includes a local web server and a host of development/deployment features

# How do I use it?
1. install [node.js and npm](https://nodejs.org/en/)
2. In your terminal or IDE cd to the devEnv root dir (the one with packages.json in it) and type "npm install" to download the js packages the project requires
3. Then type "npm start" to spool up the local web server and open index.html in your browser
4. When you make changes to the code, the project will recompile when you save and you can refresh to veiw the changes

# OK, I just want to look at the code though
The important client-side stuff is in /client/src/app
The important server-side stuff is in /server. Note that the PHP Database password has been removed for security reasons. The client uses mock data and does not require the connection