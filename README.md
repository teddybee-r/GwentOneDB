# Gwent One Database Setup

## Requirements
1. Postgres installation
2. PHP installation to run the scripts

### Setup
1. Adjust the database variables in variables.php to connect to your postgres server
2. run BuildDatabase.php to create the Database
3. run FeedDatabase.php to feed the Database
4. Wait. it takes a long time to fill up the Database (23000+ rows)

### Success
You now have a Gwent database featuring every Game version since the release of Gwent.  
```
2018-10-23  v1.0.0.15  PC release  
...         ...        ...  
2020-12-08  v8.0.0     Way of the Witcher Expansion
```
