import axios from "axios";
import "./App.css";
import React from "react";
import { DataGrid } from "@material-ui/data-grid";
import IconButton from "@material-ui/core/IconButton";
import DeleteIcon from "@material-ui/icons/Delete";
import CancelIcon from "@material-ui/icons/Cancel";
import CheckCircleIcon from "@material-ui/icons/CheckCircle";
import TextField from "@material-ui/core/TextField";
import Input from "@material-ui/core/Input";
import InputLabel from "@material-ui/core/InputLabel";
import MenuItem from "@material-ui/core/MenuItem";
import FormControl from "@material-ui/core/FormControl";
import ListItemText from "@material-ui/core/ListItemText";
import Select from "@material-ui/core/Select";
import Checkbox from "@material-ui/core/Checkbox";
import Chip from "@material-ui/core/Chip";
import { ContactSupportOutlined } from "@material-ui/icons";

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
  {
    field: "track_count",
    headerName: "Tracks",
    width: 120,
    editable: true,
  },
];

const playlists = [1, 2, 3, 4, 5, 6];

const tracks = [
  { field: "service", headerName: "service", width: 130, editable: true },
  { field: "artistName", headerName: "Artist", width: 200, editable: true },
  { field: "trackName", headerName: "Title", width: 200, editable: true },
  { field: "collectionName", headerName: "Album", width: 200, editable: true },
  {
    field: "collectionId",
    headerName: "Cover",
    width: 200,
    renderCell: (cellValues) => {
      const src =
        "http://localhost/musicguess/game/album_art/" +
        cellValues.value +
        ".jpg";
      return <img className="coverArt" src={src} />;
    },
  },
  {
    field: "previewUrl",
    headerName: "Player",
    width: 200,
    renderCell: (cellValues) => {
      return (
        <audio controls className="player">
          {" "}
          <source src={cellValues.value} type="audio/mpeg" />
        </audio>
      );
    },
  },
  {
    field: "id",
    headerName: "Delete",
    width: 130,
    renderCell: (cellValues) => {
      return (
        <IconButton
          className="delete"
          onClick={() => deleteInAllPlaylists(cellValues.value)}
          aria-label="delete"
        >
          <DeleteIcon fontSize="large" />
        </IconButton>
      );
    },
  },
  {
    field: "checked",
    headerName: "Checked",
    width: 140,
    renderCell: (cellValues) => {
      const value = cellValues.value;
      if (value === "0") {
        return (
          <IconButton
            onClick={() =>
              markTrackAsChecked(
                cellValues.row.id,
                cellValues.row.artistName,
                cellValues.row.trackName,
                cellValues.row.collectionName
              )
            }
            className="cancel"
          >
            <CancelIcon fontSize="large" />
          </IconButton>
        );
      } else {
        return (
          <IconButton className="checked">
            <CheckCircleIcon fontSize="large" />
          </IconButton>
        );
      }
    },
  },
];

function markTrackAsChecked(track_id, artist, title, album) {
  console.log("trying to mark track " + track_id + " as checked.");
  axios
    .post(
      "http://localhost/musicguess-data/mark_track_as_checked.php?id=" +
        track_id +
        "&artist=" +
        artist +
        "&title=" +
        title +
        "&album=" +
        album
    )
    .then((response) => {
      // manipulate the response here
      console.log(response);
    });
}

function deleteInAllPlaylists(track_id) {
  console.log("trying to delete track " + track_id + " in all playlists.");
  axios
    .post(
      "http://localhost/musicguess-data/delete_track_in_playlists.php?id=" +
        track_id
    )
    .then((response) => {
      // manipulate the response here
      console.log(response);
    });
}

class App extends React.Component {
  trackColumns = [
    { field: "trackId", headerName: "ID" },
    {
      field: "artistName",
      headerName: "Artist",
      width: 200,
      editable: true,
    },
    {
      field: "trackName",
      headerName: "Title",
      width: 200,
      editable: true,
    },
    {
      field: "collectionName",
      headerName: "Album",
      width: 250,
      editable: true,
    },
    {
      field: "collectionId",
      headerName: "Cover",
      width: 200,
      renderCell: (cellValues) => {
        const src =
          "http://localhost/musicguess/game/album_art/" +
          cellValues.value +
          ".jpg";
        return <img className="coverArt" src={src} />;
      },
    },
    {
      field: "releaseDate",
      headerName: "Release",
      width: 130,
      editable: false,
      valueFormatter: (params) => {
        const release = new Date(params.value.replace(/T.*/, ""));
        return (
          release.getDate() +
          "." +
          (release.getMonth() + 1) +
          "." +
          release.getFullYear()
        );
      },
    },
    {
      field: "previewUrl",
      headerName: "Player",
      width: 200,
      renderCell: (cellValues) => {
        return (
          <audio controls className="player">
            {" "}
            <source src={cellValues.value} type="audio/mpeg" />
          </audio>
        );
      },
    },
    {
      field: "playlist_ids",
      headerName: "Playlists",
      width: 300,
      editable: false,
      renderCell: (cellValues) => {
        let value = '';
        // return (<div>No playlists </div>);
        if (cellValues.value !== undefined && cellValues.value.length !== 0) {
          value = cellValues.value;
        }
        return (
          <FormControl>
            <Select
              labelId="demo-mutiple-chip-label"
              id={cellValues.row.trackId}
              multiple
              onChange={(e) => {
                this.handlePlaylistSelect(e, cellValues);
              }}
              input={<Input id="select-multiple-chip" />}
              value={cellValues.value}
              renderValue={(selected) => (
                <div>
                  {selected.map((value) => (
                    <Chip key={value} label={this.state.playlists[value].name} />
                  ))}
                </div>
              )}
            >
              {this.state.playlists.map((list) => (
                <MenuItem key={list.id} value={list.id}>
                  {list.name}
                </MenuItem>
              ))}
            </Select>
          </FormControl>
        );
      },
    },
  ];

  constructor(props) {
    super(props);
    // this.get_playlists = this.get_playlists.bind(this);
    // this.get_playlists();
    this.state = {
      playlists: [
        { id: 1, name: "Snow", public: "Jon", description: "something" },
      ],
      columns: columns,
      songs: [
        {
          trackId: 0,
          artistName: "Jon Snow",
          trackName: "Song",
          collectionName: "Album",
        },
      ],
      playlist: [{ id: 0, service: "itunessss" }],
      location: "overview",
      locationTitle: "Playlist Overview",
      selectedPlaylist: 0,
      searchArtist: "",
      searchTitle: "",
      searchAlbum: "",
      searchCountry: "",
    };

    this.goToSelectedPlaylist = this.goToSelectedPlaylist.bind(this);
    this.selectPlaylist = this.selectPlaylist.bind(this);
    this.search = this.search.bind(this);
    this.handlePlaylistSelect = this.handlePlaylistSelect.bind(this);

    axios
      .post("http://localhost/musicguess-data/playlists.php")
      .then((response) => {
        // manipulate the response here
        this.setState({ playlists: response.data });
        console.log(response.data);
      });
  }

  handlePlaylistSelect(e, cellValues) {
    const playlist_ids_selected = e.target.value;
    console.log(e);
    // change playlist ids back into names
    let ids = [];
    this.state.playlists.forEach((playlist) => {
      if(playlist_ids_selected.includes(playlist.id)){
        ids.push(playlist.id);
      }
    });

    this.setState((prevState) => ({
      songs: prevState.songs.map(
        song => song.trackId === cellValues.row.trackId? { ...song, playlist_ids: ids } : song
        )
    })); 

    axios
    .post(
      "http://localhost/musicguess-data/commit-playlists.php", {
        track_id: cellValues.row.trackId, playlist_ids : playlist_ids_selected 
      }
    )
    .then((response) => {
      // manipulate the response here
      console.log(response);
    });
  }

  search() {
    axios
      .post(
        "http://localhost/musicguess-data/search.php?artist=" +
          this.state.searchArtist +
          "&title=" +
          this.state.searchTitle +
          "&album=" +
          this.state.searchAlbum +
          "&country=" +
          this.state.searchCountry
      )
      .then((response) => {
        // manipulate the response here
        console.log(response.data.results);
        let songsWithId = response.data;
        songsWithId.forEach(function (part, index, songsWithId) {
          this[index].id = part.trackId;
        }, songsWithId);
        console.log(songsWithId);
        this.setState({
          songs: songsWithId,
          locationTitle: "Songs",
          location: "songList",
        });
      });
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
              <TextField
                id="artist"
                label="artist"
                variant="outlined"
                value={this.state.searchArtist}
                onChange={(e) => {
                  this.setState({ searchArtist: e.target.value });
                }}
              />
              <TextField
                id="title"
                label="title"
                variant="outlined"
                value={this.state.searchTitle}
                onChange={(e) => {
                  this.setState({ searchTitle: e.target.value });
                }}
              />
              <TextField
                id="album"
                label="album"
                variant="outlined"
                value={this.state.searchAlbum}
                onChange={(e) => {
                  this.setState({ searchAlbum: e.target.value });
                }}
              />
              <TextField
                id="country"
                label="country"
                variant="outlined"
                value={this.state.searchCountry}
                onChange={(e) => {
                  this.setState({ searchCountry: e.target.value });
                }}
              />
              <button onClick={this.search}>search</button>
            </React.Fragment>
          )}
          {this.state.location === "songList" && (
            <DataGrid
              rows={this.state.songs}
              columns={this.trackColumns}
              pageSize={50}
              rowsPerPageOptions={[5]}
              rowHeight={200}
            />
          )}
          {this.state.location === "singlePlaylist" && (
            <DataGrid
              rows={this.state.playlist}
              columns={tracks}
              pageSize={4}
              rowsPerPageOptions={[5]}
              rowHeight={200}
            />
          )}
          {this.state.location !== "overview" && (
            <button
              onClick={(e) =>
                this.setState({
                  location: "overview",
                  locationTitle: "Playlist Overview",
                })
              }
            >
              Go back to Overview
            </button>
          )}
        </div>
      </div>
    );
  }
}

export default App;
