<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ajax Movie Search</title>
</head>
<body>
    <h1>Ajax Movie Search</h1>

    <p><a href="list_of_movie.php">Back to Movie List</a></p>

    <label for="movie_name">Search by Movie Name:</label>
    <input type="text" id="movie_name" name="movie_name" onkeyup="searchMovie()" placeholder="Type movie name...">

    <div id="search_result" style="margin-top: 20px;"></div>

    <script>
        function searchMovie() {
            const movieName = document.getElementById('movie_name').value;
            const resultDiv = document.getElementById('search_result');

            if (movieName.trim() === '') {
                resultDiv.innerHTML = '';
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'ajax_search.php?movie_name=' + encodeURIComponent(movieName), true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    resultDiv.innerHTML = xhr.responseText;
                } else {
                    resultDiv.innerHTML = '<p>Error loading search results.</p>';
                }
            };

            xhr.send();
        }
    </script>
</body>
</html>