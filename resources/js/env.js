/**
 * Created by Xavier on 2019-03-23.
 */

const globalEnv = {
  development: {
    baseUrl: "/",
  },
  production : {
    baseUrl: "/ems/"
  }
};
export default function env(property) {
  console.log(process.env.NODE_ENV)
  return globalEnv[process.env.NODE_ENV][property];
};