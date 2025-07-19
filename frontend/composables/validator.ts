export type ValidatorType = "string" | "digit" | "name" | "password" | "email";

export const validator = (
  type: ValidatorType,
  data: string,
  name: string,
  minSize = -Infinity,
  maxSize = Infinity
) => {
  function test(regex: RegExp | null = null): string {
    if (!data) {
      return `${name} is required`;
    }
    if (data.length > maxSize) {
      return `${name} is too long`;
    }
    if (data.length < minSize) {
      return `${name} is too short`;
    }
    if (regex && !regex.test(data)) {
      return `${name} is invalid`;
    }
    return "";
  }

  switch (type) {
    case "string":
      return test(/^\p{L}+$/u);
    case "digit":
      return test(/^\d+$/);
    case "name":
      return test(/^[\p{L}-]+$/u);
    case "password":
      return test();
    case "email":
      return test(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/);
  }
};
