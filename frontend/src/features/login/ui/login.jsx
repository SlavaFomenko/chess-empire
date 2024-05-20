import React, { useState } from "react";
import styles from '../styles/login.module.scss'
import { Field, Form, Formik } from "formik";
import { loginUser } from "../model/login";
import { useDispatch } from "react-redux";

export function Login () {
  const [showPassword, setShowPassword] = useState(false);
  const dispatch = useDispatch()

  const initialValues = {
    email:'',
    password:''
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

  const toggleShowPassword = () => {
    setShowPassword(!showPassword);
  };

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

            <div>
              <Field
                type={showPassword ? "text" : "password"}
                name="password"
                placeholder="Password"
                onChange={handleChange}
                onBlur={handleBlur}
                value={values.password}
              />
              <button className={styles.showPasswordToggle} type="button" onClick={toggleShowPassword}>
                {showPassword ? "◡" : "⨀"}
              </button>
            </div>

            <button type="submit">Submit</button>
          </Form>
        )}
      </Formik>
    </div>
  );
}