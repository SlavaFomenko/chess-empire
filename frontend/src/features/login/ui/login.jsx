import React from "react";
import styles from '../styles/login.module.scss'
import { Field, Form, Formik } from "formik";
import { loginUser } from "../model/login";
import { useDispatch } from "react-redux";

export function Login () {

  const dispatch = useDispatch()

  const initialValues = {
    email:'test1@email.com',
    password:'Pass2222'
  }

  const validate = (values) => {
    const errors = {};
    if (!values.email) {
      errors.email = 'Required';
    }
    if (!values.password) {
      errors.password = 'Required';
    }
    return errors;
  }

  const onSubmitLogin = (values) => {
    dispatch(loginUser(values));
  }

  return (
    <div className={styles.login}>
      <Formik initialValues={initialValues} validate={validate} onSubmit={onSubmitLogin}>
        {({ values, handleChange, handleBlur, handleSubmit }) => (
          <Form onSubmit={handleSubmit}>
            <Field
              type="email"
              name="email"
              placeholder="Email"
              onChange={handleChange}
              onBlur={handleBlur}
              value={values.email}
            />

            <Field
              type="password"
              name="password"
              placeholder="Password"
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