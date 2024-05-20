import React from "react";
import styles from "../styles/search-game.module.scss";
import { Field, Form, Formik } from "formik";
import { useDispatch, useSelector } from "react-redux";
import { s } from "../../../shared/socket";

export function SearchGame () {
  const socketState = useSelector(store => store.socket);
  const dispatch = useDispatch();
  const initialSettings = { time: "15", rated: false, color: "r" };

  const onSubmitSearch = (values) => {
    values.time = +values.time;
    dispatch(s.searchGame(values));
  };

  return (
    <div className={styles.search_game}>
      <Formik initialValues={initialSettings} onSubmit={onSubmitSearch}>
        {({ handleChange, handleBlur, handleSubmit }) => (
          <Form onSubmit={handleSubmit}>
            <div>
              <p>Time:</p>

              <label>
                <Field
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
              <p>Time:</p>

              <label>
                <Field
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
              <p>Rated:</p>

              <label>
                <Field
                  type="checkbox"
                  name="rated"
                  onChange={handleChange}
                  onBlur={handleBlur}
                />
                Rated
              </label>
            </div>

            {socketState.state === "default" && <button type="submit">Search</button>}
            {socketState.state === "searchingGame" && <button type="button">Cancel</button>}
          </Form>
        )}
      </Formik>
    </div>
  );
}
