import { Choice } from "@/components/ui/form";

export type Config = {
    app: {
        name: string;
    };
    [key: string]: any | Config;
};
