import React, { useEffect, useState } from "react";
import axios from "axios";
import styles from "../styles/ratings-page.module.scss";
import {
  DELETE_RATING_RANGE,
  GET_RATING_RANGES,
  PATCH_RATING_RANGE,
  POST_RATING_RANGE
} from "../../../../../shared/config";
import { hideNotification, showNotification } from "../../../../../shared/notification";
import { useDispatch, useSelector } from "react-redux";
import { RatingRangeEditRow } from "../../../../../entities/admin-panel/rating-range-edit-row";
import { RatingRangeRow } from "../../../../../entities/admin-panel/rating-range-row";

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

  const createRange = () => {
    try {
      axios.post(POST_RATING_RANGE, newRange, {
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        setNewRange(null);
        fetchRatingRanges();
      }).catch(error => {dispatch(showNotification(error.response?.data?.message || "Error creating new rating range"));});
    } catch (error) {
      dispatch(showNotification("Error creating new rating range"));
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
        setEditState({ id: null, data: {} });
        fetchRatingRanges();
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
        fetchRatingRanges();
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
          {editState.id === range.id ?
            <RatingRangeEditRow
              editState={editState.data}
              setEditState={(data) => setEditState({
                ...editState,
                data: data
              })}
              onSubmit={editRange}
              onCancel={() => {setEditState({ id: null, data: null });}}
            />
            : <RatingRangeRow
              range={range}
              onEdit={() => setEditState({
                ...editState,
                id: range.id,
                data: range
              })}
              onDelete={submitDelete}
            />
          }
        </tr>)}
        {newRange &&
          <RatingRangeEditRow editState={newRange} setEditState={setNewRange} onSubmit={createRange} onCancel={() => setNewRange(null)} />}
      </table>
      {!newRange && <button
        className={styles.newRangeButton} onClick={e => setNewRange({
        minRating: 0,
        win: 10,
        loss: -10,
        title: "New Rating"
      })}
      >
        Add new rating range
      </button>}
    </div>
  );
}
