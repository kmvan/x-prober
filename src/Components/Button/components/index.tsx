import { AlertTriangle, LoaderPinwheel, Pointer, X } from 'lucide-react';
import type { AnchorHTMLAttributes, ButtonHTMLAttributes, FC } from 'react';
import styles from './index.module.scss';
import { ButtonStatus, type ButtonStatusValue } from './typings.ts';

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  status?: ButtonStatusValue;
}
interface LinkProps extends AnchorHTMLAttributes<HTMLAnchorElement> {
  status?: ButtonStatusValue;
}
const Icon: FC<{ status: ButtonStatusValue }> = ({ status }) => {
  return (
    <span className={styles.icon} data-status={status}>
      {{
        [ButtonStatus.Error]: <X />,
        [ButtonStatus.Loading]: <LoaderPinwheel />,
        [ButtonStatus.Warning]: <AlertTriangle />,
        [ButtonStatus.Pointer]: <Pointer />,
      }?.[status] ?? null}
    </span>
  );
};
export const Button: FC<ButtonProps> = ({
  status = ButtonStatus.Pointer,
  children,
  ...props
}) => {
  return (
    <button className={styles.button} type="button" {...props}>
      <Icon status={status} />
      {children}
    </button>
  );
};
export const Link: FC<LinkProps> = ({
  status = ButtonStatus.Pointer,
  children,
  ...props
}) => {
  return (
    <a className={styles.button} data-link {...props}>
      <Icon status={status} />
      {children}
    </a>
  );
};
