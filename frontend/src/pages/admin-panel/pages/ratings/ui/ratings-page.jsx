import React, { useEffect, useState } from "react";
import axios from "axios";
import styles from "../styles/ratings-page.module.scss";
import { DELETE_RATING_RANGE, GET_RATING_RANGES, PATCH_RATING_RANGE } from "../../../../../shared/config";
import { hideNotification, showNotification } from "../../../../../shared/notification";
import { useDispatch, useSelector } from "react-redux";
import editIcon from "../../../../../shared/images/icons/edit-icon.png";
import saveIcon from "../../../../../shared/images/icons/save-icon.png";
import deleteIcon from "../../../../../shared/images/icons/delete-icon.png";
import cancelIcon from "../../../../../shared/images/icons/cancel-icon.png";

export function RatingsPage (props) {
  const dispatch = useDispatch();
  const userStore = useSelector(state => state.user);
  const [ratingRanges, setRatingRanges] = useState([]);
  const [editState, setEditState] = useState({ id: null, data: {} });
  const [newRange, setNewRange] = useState(null);

  const fetchRatingRanges = () => {
    if (!userStore?.user?.token) {
      return;
    }
    try {
      axios.get(GET_RATING_RANGES, {
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        setRatingRanges(response.data);
      }).catch(error => dispatch(showNotification("Error fetching rating ranges")));
    } catch (error) {
      dispatch(showNotification("Error fetching rating ranges"));
    }
  };

  const editRange = () => {
    if (editState.id === null || ratingRanges.length === 0) {
      return;
    }
    const source = ratingRanges.find(range => range.id === editState.id);
    const keys = ["minRating", "win", "loss", "title"];
    const data = {};
    keys.forEach(key => {
      if (editState.data[key] !== source[key]) data[key] = editState.data[key];
    });

    try {
      axios.patch(PATCH_RATING_RANGE(editState.id), data, {
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        window.location.reload();
      }).catch(error => {dispatch(showNotification(error.response?.data?.message || "Error patching rating range"));});
    } catch (error) {
      dispatch(showNotification("Error patching rating range"));
    }
  };

  const deleteRange = (id) => {
    try {
      axios.delete(DELETE_RATING_RANGE(id), {
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        window.location.reload();
      }).catch(error => {dispatch(showNotification(error.response?.data?.message || "Error deleting rating range"));});
    } catch (error) {
      dispatch(showNotification("Error deleting rating range"));
    }
  };

  useEffect(() => {
    fetchRatingRanges();
  }, [userStore]);

  const submitDelete = (id) => {
    dispatch(showNotification(
      <div className={styles.deleteSubmit}>
        Are you sure you want to delete the "{ratingRanges.find(range => range.id === id).title} range"?
        <div>
          <button
            onClick={() => {
              deleteRange(id);
              dispatch(hideNotification());
            }
            }
          >Yes
          </button>
          <button
            onClick={() => {
              dispatch(hideNotification());
            }
            }
          >No
          </button>
        </div>
      </div>
    ));
  };

  // {
  //   "id": 1,
  //   "minRating": 0,
  //   "win": 30,
  //   "loss": 0,
  //   "title": "Novice"
  // }

  return (
    <div className={styles.rangesListDiv}>
      <table className={styles.rangesTable}>
        <tr className={styles.headersRow}>
          <td>Title</td>
          <td>Min Rating</td>
          <td>Win</td>
          <td>Loss</td>
        </tr>
        {ratingRanges.map(range => <tr>
          {editState.id === range.id ? <>
            <td>
              <input
                value={editState.data.title} onChange={e => setEditState({
                ...editState,
                data: { ...editState.data, title: e.target.value }
              })}
              />
            </td>
            <td>
              <input
                value={editState.data.minRating} type="number" min="0" onChange={e => setEditState({
                ...editState,
                data: { ...editState.data, minRating: e.target.value }
              })}
              />
            </td>
            <td>
              <input
                value={editState.data.win} type="number" min="1" onChange={e => setEditState({
                ...editState,
                data: { ...editState.data, win: e.target.value }
              })}
              />
            </td>
            <td>
              <input
                value={editState.data.loss} type="number" max="0" onChange={e => setEditState({
                ...editState,
                data: { ...editState.data, loss: e.target.value }
              })}
              />
            </td>
            <td>
              <img className={styles.editButton} src={saveIcon} alt="Save" onClick={editRange} />
            </td>
            <td><img
              className={styles.deleteButton} src={cancelIcon} alt="Cancel" onClick={e => setEditState({
              ...editState,
              id: null,
              data: null
            })}
            /></td>
          </> : <>
            <td>{range.title}</td>
            <td>{range.minRating}</td>
            <td className={styles.rangeWin}>{range.win}</td>
            <td className={styles.rangeLoss}>{range.loss}</td>
            <td>
              <img
                className={styles.editButton} src={editIcon} alt="Edit" onClick={() => setEditState({
                ...editState,
                id: range.id,
                data: range
              })}
              />
            </td>
            {range.minRating !== 0 && <td>
              <img className={styles.deleteButton} src={deleteIcon} alt="Delete" onClick={() => submitDelete(range.id)} />
            </td>}
          </>
          }
        </tr>)}
      </table>
      {/*<button className={styles.newRangeButton} onClick={e=>setNewRange({minRating: 0, win: 10, loss: -10, title: "New Rating"})}>*/}
      {/*  Add new rating range*/}
      {/*</button>*/}
    </div>
  );
}
