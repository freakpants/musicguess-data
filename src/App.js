import axios from "axios";
import "./App.css";
import React from "react";
import { DataGrid, useGridApiRef } from "@material-ui/data-grid";

/* 
Command to copy php scripts
cp -rf ~/musicguess-data/php/* /mnt/c/gamerbased/htdocs/musicguess-data/ */


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

const tracks= [
  { field: 'service', headerName: 'service', width: 130 },
  { field: 'artistName', headerName: 'Artist', width: 200 },
  { field: 'collectionName', headerName: 'Album', width: 200 },
];



class App extends React.Component {
  playlists;

  constructor(props) {
    super(props);
    // this.get_playlists = this.get_playlists.bind(this);
    // this.get_playlists();
    this.state = {
      playlists: [{ id: 1, name: 'Snow', public: 'Jon', description: "something" } ], 
      columns: columns,
      playlist: [{ id: 0, service: "itunessss"}],
      playlistColumns: tracks,
      location: "Playlist Overview"
   }; 

  
    this.displaySinglePlaylist = this.displaySinglePlaylist.bind(this); 

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

  displaySinglePlaylist(e){
    let id = e.id;
    this.setState({location: "Playlist " + id});
    axios
    .post("http://localhost/musicguess-data/playlist.php?id=" + id )
    .then((response) => {
      // manipulate the response here
      // this.setState({playlist: response.data.tracks});
      this.setState({playlists: response.data.tracks, columns: tracks, location: response.data[0].name });
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
        <h1>{this.state.location}</h1>
        <div style={{ height: "100vh", width: "100%" }}>
           <DataGrid
            rows={this.state.playlists}
            columns={this.state.columns}
            pageSize={20}
            rowsPerPageOptions={[5]}
            onRowClick={this.displaySinglePlaylist}
          />  
        </div>
      </div>
    );
  }
}

export default App;
