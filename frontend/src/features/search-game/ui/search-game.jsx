import React from "react";
import styles from "../styles/search-game.module.scss";
import { Field, Form, Formik } from "formik";
import { useDispatch, useSelector } from "react-redux";
import { s } from "../../../shared/socket";

export function SearchGame () {
  const socketState = useSelector(store => store.socket);
  const dispatch = useDispatch();
  const initialSettings = { time: "5", rated: false, color: "r" };

  const onSubmitSearch = (values) => {
    dispatch(s.searchGame({...values, time: +values.time}));
  };

  return (
    <div className={styles.search_game}>
      <Formik initialValues={initialSettings} onSubmit={onSubmitSearch}>
        {({ handleChange, handleBlur, handleSubmit }) => (
          <Form onSubmit={handleSubmit}>
            <div>
              <h2>Time:</h2>

              <label>
                <Field
                  disabled={socketState.state !== "default"}
                  type="radio"
                  name="time"
                  onChange={handleChange}
                  onBlur={handleBlur}
                  value="1"
                />
                1
              </label>

              <label>
                <Field
                  disabled={socketState.state !== "default"}
                  type="radio"
                  name="time"
                  onChange={handleChange}
                  onBlur={handleBlur}
                  value="5"
                />
                5
              </label>

              <label>
                <Field
                  disabled={socketState.state !== "default"}
                  type="radio"
                  name="time"
                  onChange={handleChange}
                  onBlur={handleBlur}
                  value="15"
                />
                15
              </label>
            </div>

            <div>
              <h2>Color:</h2>

              <label>
                <Field
                  disabled={socketState.state !== "default"}
                  type="radio"
                  name="color"
                  onChange={handleChange}
                  onBlur={handleBlur}
                  value="black"
                />
                Black
              </label>

              <label>
                <Field
                  disabled={socketState.state !== "default"}
                  type="radio"
                  name="color"
                  onChange={handleChange}
                  onBlur={handleBlur}
                  value="white"
                />
                White
              </label>

              <label>
                <Field
                  disabled={socketState.state !== "default"}
                  type="radio"
                  name="color"
                  onChange={handleChange}
                  onBlur={handleBlur}
                  value="r"
                />
                Random
              </label>
            </div>

            <div>
              <h2>Rating:</h2>

              <label>
                <Field
                  disabled={socketState.state !== "default"}
                  type="checkbox"
                  name="rated"
                  onChange={handleChange}
                  onBlur={handleBlur}
                />
                Rated
              </label>
            </div>

            {socketState.state === "default" && <button type="submit">Search</button>}
            {socketState.state === "searchingGame" && <button type="button" onClick={()=>{dispatch(s.cancelSearchGame())}}>Cancel</button>}
          </Form>
        )}
      </Formik>
    </div>
  );
}
