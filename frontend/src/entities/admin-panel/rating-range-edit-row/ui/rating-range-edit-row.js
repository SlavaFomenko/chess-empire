import React from "react";
import styles from "../styles/rating-range-edit-row.module.css";
import saveIcon from "../../../../shared/images/icons/save-icon.png";
import cancelIcon from "../../../../shared/images/icons/cancel-icon.png";

export function RatingRangeEditRow ({ editState, setEditState, onSubmit, onCancel }) {
  return (
    <>
      <td>
        <input
          value={editState.title} onChange={e => setEditState({ ...editState, title: e.target.value })}
        />
      </td>
      <td>
        <input
          value={editState.minRating} type="number"
          min="0"
          onChange={e => setEditState({
            ...editState, minRating: e.target.value
          })}
          onBlur={e => {
            const { value, min } = e.target;
            setEditState({
              ...editState, minRating: Math.max(Number(value), Number(min))
            });
          }}
        />
      </td>
      <td>
        <input
          value={editState.win} type="number"
          min="1"
          onChange={e => setEditState({ ...editState, win: e.target.value })}
          onBlur={e => {
            const { value, min } = e.target;
            setEditState({
              ...editState, win: Math.max(Number(value), Number(min))
            });
          }}
        />
      </td>
      <td>
        <input
          value={editState.loss} type="number"
          max="0"
          onChange={e => {
            setEditState({ ...editState, loss: e.target.value });
          }}
          onBlur={e => {
            const { value, max } = e.target;
            setEditState({
              ...editState, loss: Math.min(Number(value), Number(max))
            });
          }}
        />
      </td>
      <td>
        <img className={styles.editButton} src={saveIcon} alt="Save" onClick={onSubmit} />
      </td>
      <td><img
        className={styles.deleteButton} src={cancelIcon} alt="Cancel" onClick={onCancel}
      /></td>
    </>
  );
}
