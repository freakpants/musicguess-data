import axios from "axios";
import "./App.css";
import React from "react";
import { DataGrid } from "@material-ui/data-grid";


/* 
Command to copy php scripts
cp -rf ~/musicguess-data/php/* /mnt/c/gamerbased/htdocs/musicguess-data/ */

const columns = [
  { field: "id", headerName: "ID" },
  {
    field: "name",
    headerName: "Name",
    width: 200,
    editable: true,
  },
  {
    field: "public",
    headerName: "Public",
    width: 120,
    editable: true,
  },
  {
    field: "description",
    headerName: "Description",
    width: 500,
    editable: true,
  },
];

const tracks = [
  { field: "service", headerName: "service", width: 130, editable: true },
  { field: "artistName", headerName: "Artist", width: 200, editable: true },
  { field: "trackName", headerName: "Title", width: 200, editable: true },
  { field: "collectionName", headerName: "Album", width: 200, editable: true },
  { field: "cover", headerName: "Cover", width: 200 },
  { field: "previewUrl", headerName: "Player", width: 200,   renderCell: (cellValues) => 
    {
      return (
        <audio controls className="player"> <source src={cellValues.value} type="audio/mpeg" /></audio>
      );
    }
  },
  { field: "id", headerName: "Actions", width: 200,   renderCell: (cellValues) => 
    {
      return (
        <button onClick={() => deleteInAllPlaylists(cellValues.value)}>Delete in all Playlists</button>
      );
    }
  }
];

function deleteInAllPlaylists(track_id){
  console.log("trying to delete track " + track_id + " in all playlists.")
  axios
  .post("http://localhost/musicguess-data/delete_track_in_playlists.php?id=" + track_id)
  .then((response) => {
    // manipulate the response here
    console.log(response);
  })
}

class App extends React.Component {
  playlists;

  constructor(props) {
    super(props);
    // this.get_playlists = this.get_playlists.bind(this);
    // this.get_playlists();
    this.state = {
      playlists: [
        { id: 1, name: "Snow", public: "Jon", description: "something" },
      ],
      columns: columns,
      playlist: [{ id: 0, service: "itunessss" }],
      location: "overview",
      locationTitle: "Playlist Overview",
      selectedPlaylist: 0,
    };

    this.goToSelectedPlaylist = this.goToSelectedPlaylist.bind(this);
    this.selectPlaylist = this.selectPlaylist.bind(this);

    axios
      .post("http://localhost/musicguess-data/playlists.php")
      .then((response) => {
        // manipulate the response here
        this.setState({ playlists: response.data });
        console.log(response.data);
      })
  }

  selectPlaylist(e) {
    let id = e.id;
    this.setState({ selectedPlaylist: id });
  }

  goToSelectedPlaylist(e) {
    axios
      .post(
        "http://localhost/musicguess-data/playlist.php?id=" +
          this.state.selectedPlaylist
      )
      .then((response) => {
        // manipulate the response here
        // this.setState({playlist: response.data.tracks});
        this.setState({
          playlist: response.data.tracks,
          columns: tracks,
          locationTitle: response.data[0].name,
          location: "singlePlaylist",
        });
        console.log(response.data);
      });
  }

  render() {
    return (
      <div>
        <h1>{this.state.locationTitle}</h1>
        <div style={{ height: "85vh", width: "100%" }}>
          {this.state.location === "overview" && (
            <React.Fragment>
              <DataGrid
                rows={this.state.playlists}
                columns={this.state.columns}
                pageSize={100}
                rowsPerPageOptions={[5]}
                onRowClick={this.selectPlaylist}
              />{" "}
              <button onClick={this.goToSelectedPlaylist}>
                Go to selected Playlist
              </button>
            </React.Fragment>
          )}

          {this.state.location === "singlePlaylist" && (
            <React.Fragment>
              <DataGrid
                rows={this.state.playlist}
                columns={tracks}
                pageSize={100}
                rowsPerPageOptions={[5]}
              />
                <button onClick={(e) => this.setState({location: "overview"})}>
                Go back to Overview
              </button>
            </React.Fragment>

          )}
        </div>
      </div>
    );
  }
}

export default App;
