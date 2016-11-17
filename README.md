# nolte-test
Test project for Nolte

### Overview of task
Create a plugin able to do the following tasks:  

- Create a JSON API from  a custom post type, this should be an enpoint like: `http://example.dev/movies.json`
- Create a Custom Post Type: `movies` to create new movies and storage the data and meta fields.
- Displays the movies as a frontpage (home page of the site) using the JSON API created in the previous task, here a shortcode can be used like `[list-movies]' to display the data on the front page.

### Data / Specification
- Custom Post Type: Movie
- Fields / Meta Data of CPT
  - poster_url: a string to the url of an image associated with that movie
  - rating: a number rating / score of the value of that respective movie
  - year: date of release 
  - description: short html description of the movie
- Page should automatically display on home page
- Logic for no movies, etc
- Simple documentation for using the plugin
- API Structure should look like:

```json
{
  data: [
     {
        id: 1
        title: 'UP'
        poster_url: 'http://localhost.dev/images/up.jpg',
        rating: 5,
        year: 2010
        short_description: 'Phasellus ultrices nulla quis nibh. Quisque a lectus',
     },
     {
        id: 2
        title: 'Avatar'
        poster_url: 'http://localhost.dev/images/avatar.jpg',
        rating: 3,
        year: 2012
        short_description: 'Phasellus ultrices nulla quis nibh. Quisque a lectus',
     }
  ]
}
```
