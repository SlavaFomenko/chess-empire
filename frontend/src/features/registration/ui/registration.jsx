import React, { useState } from "react";
import styles from "../styles/registration.module.scss";
import { Field, Form, Formik } from "formik";
import { registerUser } from "../model/registration";

export function Registration ({notification, navigate}) {
  const [showPassword, setShowPassword] = useState(false);

  const initialValues = {
    email: "test1@email.com",
    username: "tim",
    first_name: "Tim",
    last_name: "Spaceman",
    password: "Pass1234",
    password_confirm: "Pass1234"
  };

  const validateForm = (values) => {
    const errors = {};
    const requiredFields = ["email", "username", "first_name", "last_name", "password", "password_confirm"];
    requiredFields.forEach(field => {
      const error = validate[field](values[field]);
      if (error) {
        errors[field] = validate[field](values[field]);
      }
    });
    if (values.password !== values.password_confirm && !errors.password_confirm) {
      errors.password_confirm = "The password confirmation does not match";
    }
    return errors;
  };

  const validate = {
    email: (value) => {
      let error;
      if (!value) {
        error = "Required";
      } else if (!/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(value)) {
        error = "Invalid email";
      }
      return error;
    },
    username: (value) => {
      let error;
      if (!value) {
        error = "Required";
      } else if (!/^[a-zA-Z0-9]+$/.test(value)) {
        error = "Username can only contain letters and digits";
      }
      return error;
    },
    first_name: (value) => {
      let error;
      if (!value) {
        error = "Required";
      } else if (!/^[a-zA-Z]+$/.test(value)) {
        error = "First name can only contain letters";
      }
      return error;
    },
    last_name: (value) => {
      let error;
      if (!value) {
        error = "Required";
      } else if (!/^[a-zA-Z]+$/.test(value)) {
        error = "Last name can only contain letters";
      }
      return error;
    },
    password: (value) => {
      let error;
      if (!value) {
        error = "Required";
      } else if (value?.length < 8) {
        error = "The password must be at least 8 characters long";
      } else if (!/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/.test(value)) {
        error = "The password must contain at least 1 digit and 1 lowercase and a capital letter";
      }
      return error;
    },
    password_confirm: (value) => {
      let error;
      if (!value) {
        error = "Required";
      }
      return error;
    }
  };

  const onSubmitRegistration = (values) => {
    const { email, username, first_name, last_name, password } = values;
    registerUser({ email, username, first_name, last_name, password }).then(data => {
      navigate("/login");
    }).catch(error => {
      notification(error);
    });
  };

  const toggleShowPassword = () => {
    setShowPassword(!showPassword);
  };

  return (
    <div className={styles.registration}>
      <Formik initialValues={initialValues} validate={validateForm} onSubmit={onSubmitRegistration}>
        {({ errors, touched, values, handleChange, handleBlur, handleSubmit }) => (
          <Form onSubmit={handleSubmit}>
            <Field
              type="email"
              name="email"
              placeholder="Email"
              onChange={handleChange}
              onBlur={handleBlur}
              value={values.email}
              validate={validate.email}
            />
            {errors.email && touched.email && <p className={styles.error}>{errors.email}</p>}

            <Field
              type="text"
              name="username"
              placeholder="Username"
              onChange={handleChange}
              onBlur={handleBlur}
              value={values.username}
              validate={validate.username}
            />
            {errors.username && touched.username && <p className={styles.error}>{errors.username}</p>}

            <Field
              type="text"
              name="first_name"
              placeholder="First name"
              onChange={handleChange}
              onBlur={handleBlur}
              value={values.first_name}
              validate={validate.first_name}
            />
            {errors.first_name && touched.first_name && <p className={styles.error}>{errors.first_name}</p>}

            <Field
              type="text"
              name="last_name"
              placeholder="Last name"
              onChange={handleChange}
              onBlur={handleBlur}
              value={values.last_name}
              validate={validate.last_name}
            />
            {errors.last_name && touched.last_name && <p className={styles.error}>{errors.last_name}</p>}

            <div>
              <Field
                type={showPassword ? "text" : "password"}
                name="password"
                placeholder="Password"
                onChange={handleChange}
                onBlur={handleBlur}
                value={values.password}
                validate={validate.password}
              />
              <button type="button" onClick={toggleShowPassword}>
                {showPassword ? "H" : "S"}
              </button>
            </div>
            {errors.password && touched.password && <p className={styles.error}>{errors.password}</p>}

            <Field
              type="password"
              name="password_confirm"
              placeholder="Confirm password"
              onChange={handleChange}
              onBlur={handleBlur}
              value={values.password_confirm}
              validate={validate.password_confirm}
            />
            {errors.password_confirm && touched.password_confirm &&
              <p className={styles.error}>{errors.password_confirm}</p>}


            <button type="submit">Submit</button>
          </Form>
        )}
      </Formik>
    </div>
  );
}