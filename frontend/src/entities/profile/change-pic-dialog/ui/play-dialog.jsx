import React from "react";
import styles from "../styles/play-dialog.module.scss";
import { BannerLayout } from "../../../../layouts/banner-layout";

export function PlayDialog ({ state, setState, onSubmit, onDelete }) {
  return (
    <BannerLayout onClick={() => setState({ ...state, selectedFile: null, opened: false })}>
      <div className={styles.changePicDialog} onClick={e => e.stopPropagation()}>
        <h2>{state.selectedFile === null ? "Select the file" : state.selectedFile.name}</h2>
        {state.selectedFile && <img src={URL.createObjectURL(state.selectedFile)} alt={state.selectedFile.name}/>}
        <div>
          <input accept=".png,.jpeg,.jpg" id="fileInput" type="file" onChange={(e) => {
            setState({...state, selectedFile: e.target.files[0]})}
          }/>
          <button onClick={onDelete}>Remove Pic</button>
          <label htmlFor="fileInput" className={styles.buttonLabel}>Choose File</label>
          <button disabled={state.selectedFile === null} onClick={onSubmit}>Submit</button>
        </div>
      </div>
    </BannerLayout>
  );
}
