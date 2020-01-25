## Challenge Idea
We have two providers collect data from them in json files we need to read and make some filter operations on them to get the result

## Backend Challenge Solution
1. create route to handle request to user controller
2. user controller use user repository to filter data
3. user repository to load data from data source
4. data provider class use as data source to load json file and map data to user data transfer object via custom mappers
5. custom mapper use transfer array to data transfer object to easy filter in repo

## Tools
1. docker
2. lumen
3. php unit test
4. Repo and AutoMapper to ability to add more data provider by small changes
