import axios from "axios";
import "./App.css";
import React from "react";
import { DataGrid } from "@material-ui/data-grid";

/* 
Command to copy php scripts
cp -rf ~/musicguess-data/php/* /mnt/c/gamerbased/htdocs/musicguess-data/ */

/* const columns = [
  { field: "id", headerName: "ID", width: 90 },
  {
    field: "name",
    headerName: "First name",
    width: 150,
    editable: true,
  },
  {
    field: "public",
    headerName: "Last name",
    width: 150,
    editable: true,
  },
  {
    field: "description",
    headerName: "Age",
    type: "number",
    width: 110,
    editable: true,
  }
]; */

const columns = [
  { field: 'id', headerName: 'ID' },
  {
    field: 'name',
    headerName: 'Name',
    width: 200,
    editable: true,
  },
  {
    field: 'public',
    headerName: 'Public',
    width: 120,
    editable: true,
  },
  {
    field: 'description',
    headerName: 'Description',
    width: 500,
    editable: true,
  }
];



class App extends React.Component {
  playlists;

  constructor(props) {
    super(props);
    // this.get_playlists = this.get_playlists.bind(this);
    // this.get_playlists();
    this.state = {playlists: [
      { id: 1, name: 'Snow', public: 'Jon', description: "something" }
    ]}; 

    axios
    .post("http://localhost/musicguess-data/playlists.php")
    .then((response) => {
      // manipulate the response here
      this.setState({playlists: response.data});
      console.log(response.data);
      /* const parsed = JSON.parse(response.data);
    if (parsed.success) {
      alert('Anfrage erfolgreich abgeschickt');
    } else {
      alert('Fehler beim Absenden der Anfrage');
      console.log(parsed.error);
    } */
    })
    .catch(function (error) {
      alert("Fehler beim Absenden der Anfrage");
      console.log(error);
      // manipulate the error response here
    });
  }
  render() {


    return (
      <div>
        Playlist Editor
        <div style={{ height: "100vh", width: "100%" }}>
          <DataGrid
            rows={this.state.playlists}
            columns={columns}
            pageSize={20}
            rowsPerPageOptions={[5]}
            checkboxSelection
            disableSelectionOnClick
          />
        </div>
      </div>
    );
  }
}

export default App;
