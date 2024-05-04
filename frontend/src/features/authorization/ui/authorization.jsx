import React from "react";
import styles from '../styles/authorization.module.scss'
import { Form, Formik } from "formik";
import { loginUser } from "../model/authorization";
import { useDispatch } from "react-redux";

export function Authorization () {

  const dispatch = useDispatch()

  const initialValues = {
    username:'',
    password:''
  }

  const validate = (values) => {
    const errors = {};
    if (!values.username) {
      errors.username = 'Required';
    }
    if (!values.password) {
      errors.password = 'Required';
    }
    return errors;
  }

  const onSubmitAuthorization = (values) => {
    dispatch(loginUser(values));
  }
  return (
    <div className={styles.authorization}>
      <Formik initialValues={initialValues} validate={validate} onSubmit={onSubmitAuthorization}>
        {({ values, handleChange, handleBlur, handleSubmit }) => (
          <Form onSubmit={handleSubmit}>
            <input
              type="text"
              name="username"
              onChange={handleChange}
              onBlur={handleBlur}
              value={values.username}
            />

            <input
              type="password"
              name="password"
              onChange={handleChange}
              onBlur={handleBlur}
              value={values.password}
            />
            <button type="submit">Submit</button>
          </Form>
        )}
      </Formik>
    </div>
  );
}