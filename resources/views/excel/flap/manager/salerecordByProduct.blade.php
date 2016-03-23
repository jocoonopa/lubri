<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </head>    
  <body>
     <!-- Headings -->
    <table>
      <thead>
        <tr>
          @foreach ($heads as $head)
            <th>{{$head}}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach ($rows as $row)
        <tr>
          @foreach ($row as $column)
          <td>{{$column}}</td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
  </body>
</html>